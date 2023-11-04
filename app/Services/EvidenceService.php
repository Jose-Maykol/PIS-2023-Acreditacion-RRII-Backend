<?php

namespace App\Services;

use App\Models\DateModel;
use App\Repositories\EvidenceRepository;
use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class EvidenceService
{

    protected $evidenceRepository;
    protected $userRepository;
    protected $standardRepository;
    public function __construct(EvidenceRepository $evidenceRepository, StandardRepository $standardRepository, UserRepository $userRepository)
    {

        $this->evidenceRepository = $evidenceRepository;
        $this->standardRepository = $standardRepository;
        $this->userRepository = $userRepository;
    }

    public function getStandardEvidences($year, $semester, $standard_id, $evidence_type_id, $parent_id)
    {
        $dateId = DateModel::dateId($year, $semester);
        $parentIdFolder = $parent_id;

        if (!$parent_id) {
            $queryRootFolder = $this->evidenceRepository
                ->haveEvidencesInFolder($standard_id, $evidence_type_id, $dateId);
            if ($queryRootFolder == null) {
                throw new \App\Exceptions\Evidence\StandardNotHaveEvidencesException();
            } else {
                $parentIdFolder = $queryRootFolder->id;
            }
        }
        $evidences = $this->evidenceRepository
            ->getStandardEvidences($parentIdFolder, $evidence_type_id, $standard_id);

        $folders = $this->evidenceRepository
            ->getStandardFolders($parentIdFolder, $evidence_type_id, $standard_id);

        if ($evidences->isEmpty() && $folders->isEmpty()) {
            throw new \App\Exceptions\Evidence\EvidencesNotFoundException();
        }

        foreach ($evidences as &$evidence) {
            $evidence['extension'] = $evidence['type'];
            unset($evidence['type']);
            $evidence['type'] = 'evidence';
        }

        foreach ($folders as &$folder) {
            $folder['type'] = 'folder';
        }

        return [
            "evidences" => $evidences,
            "folders" => $folders,
        ];
    }

    public function searchEvidence($standard_id)
    {
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        
        return $this->evidenceRepository->getEvidences($standard_id);
    }
}
