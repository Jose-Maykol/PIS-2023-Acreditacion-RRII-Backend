<?php

namespace App\Repositories;

use App\Models\EvidenceModel;
use App\Models\EvidenceTypeModel;
use App\Models\FileModel;
use App\Models\FolderModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvidenceRepository
{
    public function haveEvidencesInFolder($standard_id, $evidence_type_id, $date_id)
    {
        return FolderModel::where('standard_id', $standard_id)
            ->where('evidence_type_id', $evidence_type_id)
            ->where('date_id', $date_id)
            ->where('parent_id', null)
            ->first();
    }

    public function getStandardEvidences($parent_folder_id, $evidence_type_id, $standard_id)
    {
        return EvidenceModel::join('users', 'evidences.user_id', '=', 'users.id')
            ->where('evidences.folder_id', $parent_folder_id)
            ->where('evidences.evidence_type_id', $evidence_type_id)
            ->where('evidences.standard_id', $standard_id)
            ->select('evidences.*', DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();
    }

    public function getStandardFolders($parent_folder_id, $evidence_type_id, $standard_id)
    {
        return FolderModel::join('users', 'folders.user_id', '=', 'users.id')
            ->where('folders.parent_id', $parent_folder_id)
            ->where('folders.standard_id', $standard_id)
            ->where('folders.evidence_type_id', $evidence_type_id)
            ->select('folders.*', DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();
    }

    public function getEvidences($standard_id)
    {
        return EvidenceModel::join('files', 'evidences.file_id', '=', 'files.id')
                            ->join('folders', 'evidences.folder_id', '=', 'folders.id')
                            ->where('evidences.standard_id', $standard_id)
                            ->select('evidences.id as value', 'files.file as label', 'folders.name', 'type')
                            ->get();
    }
    public function getEvidence($evidence_id)
    {
        return EvidenceModel::with('folder.files')->where('id', $evidence_id)
            ->orWhere('file_id', $evidence_id)
            ->first();
    }

    public function existsEvidence($evidence_id)
    {
        return EvidenceModel::where('id', $evidence_id)
            ->exists();
    }
    public function exists($evidence_id)
    {
        return EvidenceModel::where($evidence_id)->exists();
    }

    public function createFileEvidence(
        $name,
        $path,
        $file,
        $type,
        $size,
        $user_id,
        $plan_id,
        $folder_id,
        $evidence_type_id,
        $standard_id,
        $date_id
    ) {
        $evidenceFile = null;

        DB::transaction(function () use (
            $name,
            $path,
            $file,
            $type,
            $size,
            $user_id,
            $plan_id,
            $folder_id,
            $evidence_type_id,
            $standard_id,
            $date_id,
            &$evidenceFile,
        ) {

            $evidenceFile = FileModel::create([
                'name' => $name,
                'path' => $path,
                'file' => $file,
                'type' => $type,
                'size' => $size,
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'folder_id' => $folder_id,
                'evidence_type_id' => $evidence_type_id,
                'standard_id' => $standard_id,
                'date_id' => $date_id
            ]);

            $latestEvidence = EvidenceModel::where('standard_id', $standard_id)
                ->where('evidence_type_id', $evidence_type_id)
                ->lockForUpdate()->latest('code')->first();
            $newCode = $latestEvidence ? $latestEvidence->code + 1 : 1;

            $evidence = EvidenceModel::create([
                'file_id' => $evidenceFile->id,
                'code' => $newCode,
                'standard_id' => $evidenceFile->standard_id,
                'evidence_type_id' => $evidenceFile->evidence_type_id,
                'date_id' => $evidenceFile->date_id
            ]);
            $evidenceFile->evidence = $evidence;
        }, 5);
        return $evidenceFile;
    }

    public function createFolderEvidence(
        $name,
        $path,
        $user_id,
        $plan_id,
        $folder_id,
        $evidence_type_id,
        $standard_id,
        $date_id
    ) {
        $folder = null;

        DB::transaction(function () use (
            $name,
            $path,
            $user_id,
            $plan_id,
            $folder_id,
            $evidence_type_id,
            $standard_id,
            $date_id,
            &$folder,
        ) {

            $folder = FolderModel::create([
                'name' => $name,
                'path' => $path,
                'user_id' => $user_id,
                'plan_id' => $plan_id,
                'parent_id' => $folder_id,
                'evidence_type_id' => $evidence_type_id,
                'standard_id' => $standard_id,
                'date_id' => $date_id
            ]);

            $latestEvidence = EvidenceModel::where('standard_id', $standard_id)
                ->where('evidence_type_id', $evidence_type_id)
                ->lockForUpdate()->latest('code')->first();
            $newCode = $latestEvidence ? $latestEvidence->code + 1 : 1;

            $evidence = EvidenceModel::create([
                'folder_id' => $folder->id,
                'code' => $newCode,
                'standard_id' => $folder->standard_id,
                'evidence_type_id' => $folder->evidence_type_id,
                'date_id' => $folder->date_id
            ]);

            $folder->evidence = $evidence;
        }, 5);
        return $folder;
    }


    public function getTypeEvidence($evidence_type_id)
    {
        return EvidenceTypeModel::find($evidence_type_id);
    }
    public function existsTypeEvidence($evidence_type_id)
    {
        return EvidenceTypeModel::where('id', $evidence_type_id)->exists();
    }

    public function existsEvidenceFolder($standard_id, $evidence_type_id, $date_id, $folder_id)
    {
        return EvidenceModel::where('date_id', $date_id)
            ->where('standard_id', $standard_id)
            ->where('evidence_type_id', $evidence_type_id)
            ->where('folder_id', $folder_id)
            ->exists();
    }
    public function existsEvidenceFile($standard_id, $evidence_type_id, $date_id, $file_id)
    {
        return EvidenceModel::where('date_id', $date_id)
            ->where('standard_id', $standard_id)
            ->where('evidence_type_id', $evidence_type_id)
            ->where('file_id', $file_id)
            ->exists();
    }
    public function moveFile($file, $newFolder, $pathRoot)
    {
        $oldPath = $file->path;

        $newPath = $newFolder ? ($newFolder->path . '/' . $file->file) : ('/' . $file->file);
        if (Storage::exists($pathRoot . $oldPath)) {
            Storage::move($pathRoot . $oldPath, $pathRoot . $newPath);
        }

        $file->folder_id = $newFolder ? $newFolder->id : null;
        $file->path = $newPath;
        $file->save();
        return $file;
    }
    
}
