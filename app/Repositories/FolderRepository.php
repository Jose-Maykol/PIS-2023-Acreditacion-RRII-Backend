<?php

namespace App\Repositories;

use App\Models\Evidence;
use App\Models\EvidenceModel;
use App\Models\Folder;
use App\Models\FolderModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FolderRepository
{
    public function getFolder($evidence_id)
    {
        return FolderModel::find($evidence_id);
    }

    public function createFolder($name, $path, $userId, $standardId, $typeEvidenceId, $dateId, $folder_id, $plan_id)
    {
        $folder = new FolderModel([
            'name' => $name,
            'user_id' => $userId,
            'path' => $path,
            'standard_id' => $standardId,
            'evidence_type_id' => $typeEvidenceId,
            'parent_id' => $folder_id,
            'plan_id' => $plan_id,
            'date_id' => $dateId,
        ]);
        $folder->save();
        return $folder;
    }
    public function exists($folder_id)
    {
        return FolderModel::where('id', $folder_id)->exists();
    }

    public function existsFolder($relativePath, $standardId, $typeEvidenceId)
    {
        return FolderModel::where('path', $relativePath)
            ->where('standard_id', $standardId)
            ->where('evidence_type_id', $typeEvidenceId)
            ->exists();
    }

    public function deleteFolderAndContents($folderId, $dateId, $standardId, $typeEvidenceId, $pathRoot)
    {
        DB::transaction(function () use ($folderId, $dateId, $standardId, $typeEvidenceId, $pathRoot) {
            $this->deleteFolderRecursively($folderId, $pathRoot);
            $this->reassignEvidencesCodes($dateId, $standardId, $typeEvidenceId);
        }, 5);
    }

    private function deleteFolderRecursively($folderId, $pathRoot)
    {
        $folder = FolderModel::with(['subfolders', 'files'])->lockForUpdate()->find($folderId);

        if ($folder) {
            foreach ($folder->subfolders as $subfolder) {
                $this->deleteFolderRecursively($subfolder->id, $pathRoot);
            }

            foreach ($folder->files as $file) {
                Storage::delete($pathRoot . $file->path);
                $file->delete();
            }

            Storage::deleteDirectory($pathRoot . $folder->path);

            $folder->delete();
        }
    }
    private function reassignEvidencesCodes($dateId, $standardId, $typeEvidenceId)
    {
        $evidences = EvidenceModel::where('date_id', $dateId)
            ->where('standard_id', $standardId)
            ->where('evidence_type_id', $typeEvidenceId)
            ->orderBy('code')
            ->lockForUpdate()
            ->get();
        $nextCode = 1;

        foreach ($evidences as $evidence) {
            if ($evidence->code != $nextCode) {
                $evidence->code = $nextCode;
                $evidence->save();
            }
            $nextCode++;
        }
    }
    public function renameFolder($folderId, $newName, $pathRoot)
    {
        $folder = null;
        DB::transaction(function () use ($folderId, $newName, $pathRoot, &$folder) {
            $folder = FolderModel::where('id', $folderId)->lockForUpdate()->first();
            $oldPath = $folder->path;
            $newPath = $this->getNewPath($oldPath, $newName);

            if (Storage::exists($pathRoot . $oldPath)) {
                Storage::move($pathRoot . $oldPath, $pathRoot . $newPath);
            }

            $folder->name = $newName;
            $folder->path = $newPath;
            $folder->save();

            $this->updateChildPaths($folder, $oldPath, $newPath, $pathRoot);
        }, 5);
        return $folder;
    }

    private function getNewPath($oldPath, $newName)
    {
        $pathParts = explode('/', $oldPath);

        array_pop($pathParts);

        $pathParts[] = $newName;

        return implode('/', $pathParts);
    }
    private function updateChildPaths($folder, $oldPath, $newPath, $pathRoot)
    {
        foreach ($folder->subfolders as $subfolder) {
            $subfolderNewPath = str_replace($oldPath, $newPath, $subfolder->path);

            if (Storage::exists($pathRoot . $subfolder->path)) {
                Storage::move($pathRoot . $subfolder->path, $pathRoot . $subfolderNewPath);
            }

            $subfolder->path = $subfolderNewPath;
            $subfolder->save();

            $this->updateChildPaths($subfolder, $oldPath, $newPath, $pathRoot);
        }

        foreach ($folder->files as $file) {
            $fileNewPath = str_replace($oldPath, $newPath, $file->path);

            if (Storage::exists($pathRoot . $file->path)) {
                Storage::move($pathRoot . $file->path, $pathRoot . $fileNewPath);
            }

            $file->path = $fileNewPath;
            $file->save();
        }
    }

    public function moveFolder($folderId, $newParentId, $pathRoot)
    {
        $folder = null;
        DB::transaction(function () use ($folderId, $newParentId, $pathRoot, &$folder) {
            $folder = FolderModel::where('id', $folderId)->lockForUpdate()->first();
            $newParentFolder = null;
            if($newParentId){
                $newParentFolder = FolderModel::where('id', $newParentId)->lockForUpdate()->first();
            }
            $oldPath = $folder->path;

            $newPath = $newParentFolder ? ($newParentFolder->path . '/' . $folder->name) : ('/' . $folder->name);
            if (Storage::exists($pathRoot . $oldPath)) {
                Storage::move($pathRoot . $oldPath, $pathRoot . $newPath);
            }

            $folder->parent_id = $newParentId;
            $folder->path = $newPath;
            $folder->save();

            $this->updateChildPaths($folder, $oldPath, $newPath, $pathRoot);
        });
        return $folder;
    }
}
