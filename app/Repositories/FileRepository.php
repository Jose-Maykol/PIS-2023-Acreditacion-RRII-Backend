<?php

namespace App\Repositories;

use App\Models\EvidenceModel;
use App\Models\FileModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileRepository
{
    public function getFile($file_id)
    {
        return FileModel::find($file_id);
    }

    public function existsFile($relativePath, $standardId, $typeEvidenceId, $dateId)
    {
        return FileModel::where('path', $relativePath)
            ->where('date_id', $dateId)
            ->where('standard_id', $standardId)
            ->where('evidence_type_id', $typeEvidenceId)
            ->exists();
    }
    public function existsFileInFolder($relativePath, $standardId, $typeEvidenceId, $dateId, $folder_id)
    {
        return FileModel::where('folder_id', $folder_id)
            ->where('path', $relativePath)
            ->where('date_id', $dateId)
            ->where('standard_id', $standardId)
            ->where('evidence_type_id', $typeEvidenceId)
            ->exists();
    }
    public function existsFileId($file_id)
    {
        return FileModel::where('id', $file_id)
            ->exists();
    }
    public function createFile(
        $name,
        $path,
        $file,
        $type,
        $size,
        $userId,
        $plan_id,
        $folder_id,
        $evidence_type_id,
        $standard_id,
        $date_id
    ) {
        $file = new FileModel([
            'name' => $name,
            'path' => $path,
            'file' => $file,
            'type' => $type,
            'size' => $size,
            'user_id' => $userId,
            'plan_id' => $plan_id,
            'folder_id' => $folder_id,
            'evidence_type_id' => $evidence_type_id,
            'standard_id' => $standard_id,
            'date_id' => $date_id
        ]);
        $file->save();
        return $file;
    }

    public function deleteFile($file_id, $dateId, $standardId, $typeEvidenceId, $pathRoot)
    {
        DB::transaction(function () use ($file_id, $dateId, $standardId, $typeEvidenceId, $pathRoot) {
            $this->deleteFileAndStorage($file_id, $pathRoot);
            $this->reassignEvidencesCodes($dateId, $standardId, $typeEvidenceId);
        }, 5);
    }

    private function deleteFileAndStorage($file_id, $pathRoot)
    {
        $file = FileModel::lockForUpdate()->find($file_id);
        if (Storage::exists($pathRoot . $file->path)) {
            Storage::delete($pathRoot . $file->path);
        }

        $file->delete();
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
}
