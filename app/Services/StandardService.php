<?php

namespace App\Services;

use App\Models\DateModel;
use App\Models\EvidenceTypeModel;
use App\Repositories\DateSemesterRepository;
use App\Repositories\EvidenceRepository;
use App\Repositories\FolderRepository;
use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;
use App\Services\GoogleDriveService;

use Illuminate\Http\Request;

class StandardService
{

    protected $standardRepository;
    protected $userRepository;
    protected $folderRepository;
    protected $evidenceRepository;
    protected $dateRepository;
    protected $googleDriveService;

    public function __construct(
        DateSemesterRepository $dateRepository,
        EvidenceRepository $evidenceRepository,
        StandardRepository $standardRepository,
        UserRepository $userRepository,
        FolderRepository $folderRepository,
        GoogleDriveService $googleDriveService
    ) {
        $this->dateRepository = $dateRepository;
        $this->folderRepository = $folderRepository;
        $this->evidenceRepository = $evidenceRepository;
        $this->standardRepository = $standardRepository;
        $this->userRepository = $userRepository;
        $this->googleDriveService = new $googleDriveService;
    }

    public function listStandardsAssignment($year, $semester)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        $standards = $this->standardRepository->listStandardsAssignment($year, $semester);
        return [
            'standards' => $standards,
            "isSemesterClosed" => $this->dateRepository->isSemesterClosed($year, $semester)
        ];
    }

    public function listStandardHeaders($year, $semester)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        return $this->standardRepository->listStandardHeaders($year, $semester);
    }

    public function listPartialStandards($year, $semester)
    {
        return $this->standardRepository->listPartialStandards($year, $semester);
    }

    public function changeStandardAssignment($standard_id, Request $request)
    {
        $userAuth = auth()->user();

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if ($this->standardRepository->getDocumentId($standard_id) != null) {
            foreach ($request->users as $user) {
                $user = $this->userRepository->getUserById($user);
                $userEmail = $user->email;
                $documentId = $this->standardRepository->getDocumentId($standard_id);
                $this->googleDriveService->shareGoogleDoc($documentId, $userEmail);
            }
        }
        return $this->standardRepository->changeStandardAssignment($standard_id, $request->users);
    }

    public function showStandard($standard_id)
    {
        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        $standard = $this->standardRepository->getFullStandard($standard_id);
        //$standard->standardStatus = $this->standardRepository->getAllStandardStatus();
        $standard->isManager = $this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth);
        $standard->isAdministrator = $this->userRepository->isAdministrator($userAuth);
        return $standard;
    }

    public function updateStandardHeader($standard_id, Request $request)
    {
        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if (!($this->userRepository->isAdministrator($userAuth)
            or $this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth))) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        return $this->standardRepository->updateStandardHeader($standard_id, $request->description, $request->factor, $request->dimension, $request->related_standards);
    }

    public function updateStandardStatus($standard_id, $standard_status_id)
    {

        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if (!$this->standardRepository->getStandardStatusActiveById($standard_status_id)) {
            throw new \App\Exceptions\Standard\StandardStatusNotFoundException();
        }

        if (!$this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        return $this->standardRepository->updateStandardStatus($standard_id, $standard_status_id);
    }

    public function listUserAssigned($standard_id)
    {

        $userAuth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if (!$this->userRepository->isAdministrator($userAuth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        $users = $this->userRepository->getAllUsersActive();

        foreach ($users as $user) {
            if ($user->providers()->first() !== null) {
                $user->avatar = $user->providers()->first()->avatar;
            } else {
                $user->avatar = null;
            }

            $user->isManager = $this->userRepository->checkIfUserIsManagerStandard($standard_id, $user);
        }
        return $users;
    }

    public function listStandardStatus($standard_id = 0)
    {
        $userAuth = auth()->user();
        if ($standard_id != 0 and !$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }

        if ($standard_id != 0 and !($this->userRepository->isAdministrator($userAuth)
            or $this->userRepository->checkIfUserIsManagerStandard($standard_id, $userAuth)
        )) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        $standardStatus = $this->standardRepository->getAllStandardStatus();
        return $standardStatus;
    }

    public function getStandardEvidences(Request $request)
    {
        $year = $request->route('year');
        $semester = $request->route('semester');

        $standard_id = $request->route('standard_id');
        $evidence_type_id = $request->route('evidence_type_id');
        $parent_folder_id = $request->parent_id;
        $plan_id = $request->plan_id;

        $dateId = DateModel::dateId($year, $semester);
        if ($parent_folder_id && !$this->folderRepository->exists($parent_folder_id)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }

        $evidencesQuery = $this->evidenceRepository->getStandardEvidences($parent_folder_id, $evidence_type_id, $standard_id);

        if ($plan_id) {
            $evidencesQuery->where('files.plan_id', $plan_id);
        }

        $evidences = $evidencesQuery->get();

        foreach ($evidences as &$file) {
            if ($this->evidenceRepository->existsEvidenceFileId($file->file_id)) {
                $evidence = $this->evidenceRepository->getEvidenceFile($file->file_id);
                $file->evidence_code = $this->codeFormat($standard_id, $evidence_type_id, $evidence->code);
                $file->evidence_id = $evidence->id;
            }
        }

        $folders = $this->evidenceRepository->getStandardFolders($parent_folder_id, $evidence_type_id, $standard_id);

        foreach ($folders as &$folder) {
            if ($this->evidenceRepository->existsEvidenceFolderId($folder->folder_id)) {
                $evidence = $this->evidenceRepository->getEvidenceFolder($folder->folder_id);
                $folder->evidence_code = $this->codeFormat($standard_id, $evidence_type_id, $evidence->code);
                $folder->evidence_id = $evidence->id;
            }
        }

        foreach ($evidences as &$evidence) {
            unset($evidence['type']);
            $evidence['type'] = 'evidence';
        }

        foreach ($folders as &$folder) {
            $folder['type'] = 'folder';
        }

        return [
            "isManager" => $this->userRepository->checkIfUserIsManagerStandard($standard_id, auth()->user()),
            "evidences" => $evidences,
            "folders" => $folders,
        ];
    }
    public function codeFormat($standard_id, $evidence_type_id, $nro_code)
    {
        $code = "E";
        if ($evidence_type_id == EvidenceTypeModel::getPlanificationId()) {
            $code = $code . "P.";
        }
        if ($evidence_type_id == EvidenceTypeModel::getResultId()) {
            $code = $code . "R.";
        }
        /*
        if($evidence_type_id == EvidenceTypeModel::getImprovementId()){
            $code = $code . "M";
        }*/
        $standard = $this->standardRepository->getStandardActiveById($standard_id);

        if ($nro_code < 10) {
            $nro_code = "0" . $nro_code;
        }
        $code = $code . "E" . $standard->nro_standard . "." . $nro_code;
        return $code;
    }

    public function activateNarrative(Request $request)
    {
        $standard_id = $request->standard_id;
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        $standard = $this->standardRepository->activateNarrative($standard_id);
        return $standard;
    }

    public function blockNarrative(Request $request)
    {
        $standard_id = $request->route('standard_id');
        $user_auth = auth()->user();

        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if (!$this->userRepository->checkIfUserIsManagerStandard($standard_id, $user_auth)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }

        if ($this->standardRepository->isBeingEdited($standard_id)) {
            $user = $this->standardRepository->getUserBlockNarrative($standard_id);

            if ($user->providers()->first() !== null) {
                $user->avatar = $user->providers()->first()->avatar;
            } else {
                $user->avatar = null;
            }
            $block_user = [
                'user_name' => $user->name . ' ' . $user->lastname,
                'user_email' => $user->email,
                'user_avatar' => $user->avatar,
                'is_block' => $this->standardRepository->isBeingEdited($standard_id),
                'is_same_user' => ($user->id == $user_auth->id) ? true : false
            ];
            return $block_user;
        }
        $user_standard = $this->standardRepository->blockNarrative($standard_id, $user_auth->id);
        return $this->standardRepository->getStandardActiveById($standard_id);
    }

    public function unlockNarrative(Request $request)
    {
        $standard_id = $request->route('standard_id');
        $user = auth()->user();
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if (!$this->userRepository->checkIfUserIsManagerStandard($standard_id, $user)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        $user_standard = $this->standardRepository->unblockNarrative($standard_id, $user->id);
        return $user_standard;
    }

    public function enableNarrative($year, $semester, Request $request)
    {
        $standard_id = $request->route('standard_id');
        $user = auth()->user();
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if (!$this->userRepository->checkIfUserIsManagerStandard($standard_id, $user)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        if ($this->standardRepository->getDocumentId($standard_id)) {
            throw new \App\Exceptions\Standard\NarrativeIsAlreadyExistException();
        }
        $path = 'narrativas/' . $year . '/' . $semester;
        $folder_id = $this->googleDriveService->createFolder($path);
        $currentStandard = $this->standardRepository->getStandardById($standard_id);
        $nro_standard = $currentStandard->nro_standard;
        $doc_id = $this->googleDriveService->createGoogleDoc("Estándar " . $nro_standard . " - Narrativa", $folder_id);
        $users = $this->standardRepository->getUsersStandard($standard_id);
        foreach ($users as $user) {
            $user = $this->userRepository->getUserById($user->user_id);
            $this->googleDriveService->shareGoogleDoc($doc_id, $user->email);
        }
        $this->standardRepository->saveDocumentId($standard_id, $doc_id);
        $standard = $this->standardRepository->enableNarrative($standard_id);
        return $standard;
    }

    public function linkEvidenceToNarrative($year, $semester, Request $request)
    {
        $standard_id = $request->route('standard_id');
        $user = auth()->user();
        if (!$this->standardRepository->getStandardActiveById($standard_id)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if (!$this->userRepository->checkIfUserIsManagerStandard($standard_id, $user)) {
            throw new \App\Exceptions\User\UserNotAuthorizedException();
        }
        $doc_id = $this->standardRepository->getDocumentId($standard_id);
        $evidence_code = $request->evidence_code;
        $url_evidence = $request->url_evidence;
        $this->googleDriveService->insertLinkToDocument($doc_id, $evidence_code, $url_evidence);
        return "Link insertado";
    }

    public function narrativeIsEnabled($standard_id)
    {
        if ($this->standardRepository->narrativeIsEnabled($standard_id)) {
            throw new \App\Exceptions\Standard\NarrativeIsEnabledException();
        }
        return null;
    }
}
