<?php

namespace App\Repositories;

use App\Models\Evidence;
use App\Models\Folder;
use Illuminate\Support\Facades\DB;

class EvidenceRepository
{
    public function haveEvidencesInFolder($standard_id, $evidence_type_id, $date_id){
        return Folder::where('standard_id', $standard_id)
                                        ->where('evidence_type_id', $evidence_type_id)
                                        ->where('date_id', $date_id)
                                        ->where('parent_id', null)
                                        ->first();
    }

    public function getStandardEvidences($parent_folder_id, $evidence_type_id, $standard_id ){
        return Evidence::join('users', 'evidences.user_id', '=', 'users.id')
            ->where('evidences.folder_id', $parent_folder_id)
            ->where('evidences.evidence_type_id', $evidence_type_id)
            ->where('evidences.standard_id', $standard_id)
            ->select('evidences.*', DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();
    }

    public function getStandardFolders($parent_folder_id, $evidence_type_id, $standard_id){
        return Folder::join('users', 'folders.user_id', '=', 'users.id')
            ->where('folders.parent_id', $parent_folder_id)
            ->where('folders.standard_id', $standard_id)
            ->where('folders.evidence_type_id', $evidence_type_id)
            ->select('folders.*', DB::raw("CONCAT(users.name, ' ', users.lastname) as full_name"))
            ->get();
    }

    public function getEvidences($standard_id){
        return Evidence::where('standard_id', $standard_id)
            ->select('id as value', 'file as label', 'type')
            ->get();
    }
}