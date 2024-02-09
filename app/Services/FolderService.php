<?php

namespace App\Services;

use App\Models\DateModel;
use App\Models\EvidenceTypeModel;
use App\Models\FileModel;
use App\Models\FolderModel;
use App\Repositories\DateSemesterRepository;
use App\Repositories\EvidenceRepository;
use App\Repositories\FacultyStaffRepository;
use App\Repositories\FolderRepository;
use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FolderService
{

    protected $userRepository;
    protected $dateSemesterRepository;
    protected $folderRepository;
    protected $standardRepository;
    protected $evidenceRepository;
    protected $standardService;
    public function __construct(StandardService $standardService, EvidenceRepository $evidenceRepository, FolderRepository $folderRepository, StandardRepository $standardRepository, UserRepository $userRepository, DateSemesterRepository $dateSemesterRepository)
    {
        $this->standardService = $standardService;
        $this->standardRepository = $standardRepository;
        $this->folderRepository = $folderRepository;
        $this->dateSemesterRepository = $dateSemesterRepository;
        $this->userRepository = $userRepository;
        $this->evidenceRepository = $evidenceRepository;
    }

    public function createFolder(Request $request)
    {
        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request->route('semester');
        $dateId = DateModel::dateId($year, $semester);

        $name = $request->name;
        $standardId = $request->standard_id;
        $typeEvidenceId = $request->type_evidence_id;
        $planId = $request->has('plan_id') ? $request->plan_id : null;
        //$generalPath = $request->has('path') ? $request->path : '/';
        $parentFolderId = $request->has('folder_id') ? $request->folder_id : null;
        $is_evidence = $request->is_evidence;
        $generalPath = '/';

        if (!$this->standardRepository->getStandardActiveById($standardId)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if ($parentFolderId && !$this->folderRepository->exists($parentFolderId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }
        if (!$this->evidenceRepository->existsTypeEvidence($typeEvidenceId)) {
            throw new \App\Exceptions\Evidence\EvidenceTypeNotFoundException();
        }
        if ($planId && $typeEvidenceId != EvidenceTypeModel::getImprovementId()) {
            throw new \App\Exceptions\Evidence\EvidenceTypeNotFoundException();
        }

        $parentfolder = null;
        if ($parentFolderId) {
            $parentfolder = $this->folderRepository->getFolder($parentFolderId);
            $generalPath = $parentfolder->path;
        }

        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $pathRoot = storage_path('app/evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description);
        $relativePath = $generalPath == '/' ? $generalPath . $name : $generalPath . '/' . $name;
        $generalPath = $generalPath == '/' ? '' : $generalPath;

        $pathNewFolder = $pathRoot . $relativePath;

        if (File::exists($pathNewFolder) && $this->folderRepository->existsFolder($relativePath, $standardId, $typeEvidenceId)) {
            throw new \App\Exceptions\Evidence\EvidenceAlreadyExistsException(("Ya existe la carpeta: " . $name), null);
        }

        if ($parentFolderId &&$this->evidenceRepository->existsEvidenceFolder($standard->id, $evidence_type->id, $dateId, $parentFolderId)){
            throw new \App\Exceptions\Evidence\FolderCannotHaveEvidencesException("Un folder evidencia no puede tener subfolder.");
        }

        $folder = null;
        if (!$planId && $is_evidence) {
            $folder = $this->evidenceRepository->createFolderEvidence(
                $name,
                $relativePath,
                $userId,
                $planId,
                $parentfolder ? $parentfolder->id : null,
                $typeEvidenceId,
                $standardId,
                $dateId
            );
        } else {
            $folder = $this->folderRepository->createFolder(
                $name,
                $relativePath,
                $userId,
                $standardId,
                $typeEvidenceId,
                $dateId,
                $parentfolder ? $parentfolder->id : null,
                $planId
            );
        }
        File::makeDirectory($pathNewFolder, 0777, true);


        return $folder;
    }

    public function renameFolder(Request $request)
    {
        $folderId = $request->route('folder_id');
        $year = $request->route('year');
        $semester = $request->route('semester');
        $dateId = DateModel::dateId($year, $semester);


        if ($folderId && !$this->folderRepository->exists($folderId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }


        $folder = $this->folderRepository->getFolder($folderId);
        $newFolderName = $request->new_name;
        $standardId = $folder->standard_id;
        $typeEvidenceId = $folder->evidence_type_id;

        if (!$this->standardRepository->getStandardActiveById($standardId)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if (!$this->evidenceRepository->existsTypeEvidence($typeEvidenceId)) {
            throw new \App\Exceptions\Evidence\EvidenceTypeNotFoundException();
        }
        $generalPath = '/';

        $parentfolder = null;
        if ($folder->parent_id) {
            $parentfolder = $this->folderRepository->getFolder($folder->parent_id);
            $generalPath = $parentfolder->path;
        }
        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;

        $pathRoot = storage_path('app/' . $currentPath);
        $relativePath = $generalPath == '/' ? $generalPath . $newFolderName : $generalPath . '/' . $newFolderName;
        $generalPath = $generalPath == '/' ? '' : $generalPath;

        $pathNewFolder = $pathRoot . $relativePath;
        if (File::exists($pathNewFolder) && $this->folderRepository->existsFolder($relativePath, $standardId, $typeEvidenceId)) {
            throw new \App\Exceptions\Evidence\EvidenceAlreadyExistsException(("Ya existe la carpeta con el nombre: " . $newFolderName), null);
        }

        $result = $this->folderRepository->renameFolder($folderId, $newFolderName, $currentPath);

        return $result;
    }

    public function moveFolder(Request $request)
    {
        $folderId = $request->route('folder_id');
        $year = $request->route('year');
        $semester = $request->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        $parentId = $request->parent_id ? $request->parent_id : null;

        if ($parentId && !$this->folderRepository->exists($parentId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }

        $parentFolder = $parentId ? $this->folderRepository->getFolder($parentId) : null;

        if ($parentFolder && $parentFolder->id == $folderId) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover la carpeta dentro de si misma");
        }

        if (!$this->folderRepository->exists($folderId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }
        $folder = $this->folderRepository->getFolder($folderId);

        if ($parentFolder && $parentFolder->evidence_type_id != $folder->evidence_type_id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover la carpeta a una carpeta de otro tipo de evidencia.");
        }
        if ($parentFolder && $parentFolder->date_id != $folder->date_id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover la carpeta a una carpeta de otro periodo.");
        }

        if ($parentFolder && $parentFolder->standard_id != $folder->standard_id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover la carpeta a una carpeta de otro estándar.");
        }
        $this->standardService->narrativeIsEnabled($folder->standard_id);

        if ($parentFolder && $this->evidenceRepository->existsEvidenceFolderId($parentFolder->id) && $this->evidenceRepository->existsEvidenceFolderId($folder->id)){
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover la carpeta evidencia a otra carpeta evidencia.");
        }
        if ($parentFolder && $this->evidenceRepository->existsEvidenceFolderId($parentFolder->id) && !$this->evidenceRepository->existsEvidenceFolderId($folder->id)){
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover la carpeta a una carpeta evidencia.");
        }

        $standardId = $folder->standard_id;
        $typeEvidenceId = $folder->evidence_type_id;

        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;
        $folder = $this->folderRepository->moveFolder($folderId, $parentId, $currentPath);

        return $folder;
    }

    public function listFolder(Request $request)
    {
        $standardId = $request->standard_id;
        $evidenceTypeId = $request->evidence_type_id;
        $folderId = $request->folder_id;
        $year = $request->route('year');
        $semester = $request->route('semester');
        $dateId = DateModel::dateId($year, $semester);
        if ($folderId && !$this->folderRepository->exists($folderId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }
        $folders = null;
        if ($folderId == null) {
            $folders = FolderModel::where('parent_id', null)
                ->where('standard_id', $standardId)
                ->where('evidence_type_id', $evidenceTypeId)
                ->where('date_id', $dateId)
                ->get();
            if (!$folders) {
                throw new \App\Exceptions\Evidence\FolderNotFoundException("No se encontraron carpetas");
            }
        } else {
            $folder = FolderModel::find($folderId);

            if (!$folder) {
                throw new \App\Exceptions\Evidence\FolderNotFoundException("No se existe la carpeta");
            }

            $folders = FolderModel::where('parent_id', $folderId)->get();

            if ($folders->isEmpty()) {
                throw new \App\Exceptions\Evidence\FolderNotFoundException("No hay carpetas dentro de esta carpeta");
            }
        }

        return $folders;
    }

    public function deleteFolder($year, $semester, $folder_id)
    {
        $dateId = DateModel::dateId($year, $semester);

        $folderId = $folder_id;

        if ($folderId && !$this->folderRepository->exists($folderId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }
        $folder = $this->folderRepository->getFolder($folderId);
        $this->standardService->narrativeIsEnabled($folder->standard_id);

        $standardId = $folder->standard_id;
        $typeEvidenceId = $folder->evidence_type_id;

        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;

        $result = $this->folderRepository->deleteFolderAndContents($folderId, $dateId, $standardId, $typeEvidenceId, $currentPath);

        return $result;
    }
}
