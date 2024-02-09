<?php

namespace App\Services;

use App\Models\DateModel;
use App\Models\EvidenceModel;
use App\Models\EvidenceTypeModel;
use App\Models\StandardModel;
use App\Models\Folder;
use App\Repositories\EvidenceRepository;
use App\Repositories\FileRepository;
use App\Repositories\FolderRepository;
use App\Repositories\PlanRepository;
use App\Repositories\StandardRepository;
use App\Repositories\UserRepository;
use App\Repositories\DateSemesterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class EvidenceService
{

    protected $evidenceRepository;
    protected $userRepository;
    protected $standardRepository;
    protected $folderRepository;
    protected $fileRepository;
    protected $planRepository;
    protected $dateRepository;
    protected $standardService;
    public function __construct(StandardService $standardService, PlanRepository $planRepository, FileRepository $fileRepository, EvidenceRepository $evidenceRepository, StandardRepository $standardRepository, UserRepository $userRepository, FolderRepository $folderRepository, DateSemesterRepository $dateRepository)
    {
        $this->standardService = $standardService;
        $this->folderRepository = $folderRepository;
        $this->planRepository = $planRepository;
        $this->evidenceRepository = $evidenceRepository;
        $this->standardRepository = $standardRepository;
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
        $this->dateRepository = $dateRepository; 
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
            "isManager" => $this->userRepository->checkIfUserIsManagerStandard($standard_id, auth()->user()),
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

    public function showEvidence($evidence_id)
    {
        if (!$this->evidenceRepository->exists($evidence_id)) {
            throw new \App\Exceptions\Evidence\EvidenceNotFoundException();
        }
        $evidence = $this->evidenceRepository->getEvidence($evidence_id);
        return $evidence;
    }

    public function viewFile($year, $semester, $file_id)
    {
        if (!$this->fileRepository->existsFileId($file_id)) {
            throw new \App\Exceptions\Evidence\FileNotFoundException();
        }
        $file = $this->fileRepository->getFile($file_id);

        $standard = $this->standardRepository->getStandardActiveById($file->standard_id);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($file->evidence_type_id);

        $pathRoot = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;
        $path = storage_path('app/' . $pathRoot . $file->path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $fileContents = file_get_contents($path);
        $base64Content = base64_encode($fileContents);

        return [
            "name" => $file->name,
            "extension" => $extension,
            "content" => $base64Content
        ];
    }

    public function viewEvidence($year, $semester, $evidence_id)
    {
        if (!$this->evidenceRepository->existsEvidence($evidence_id)) {
            throw new \App\Exceptions\Evidence\EvidenceNotFoundException();
        }
        $evidence = $this->evidenceRepository->getEvidence($evidence_id);

        $standard = $this->standardRepository->getStandardActiveById($evidence->standard_id);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($evidence->evidence_type_id);

        $pathRoot = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;

        $data = null;
        if ($evidence->file_id) {
            //$path = storage_path('app/' . $pathRoot . $evidence->file->path);
            //$extension = pathinfo($path, PATHINFO_EXTENSION);
            //$fileContents = file_get_contents($path);
            //$base64Content = base64_encode($fileContents);
            /*$evidence_array = array(
                "name" => $evidence->file->name,
                "extension" => $extension,
                "content" => $base64Content
            );*/
            $data = [$evidence->file];
        } elseif ($evidence->folder_id) {
            $data = $evidence->folder->files;
        }

        return $data;
    }

    public function downloadFile($year, $semester, $file_id)
    {
        if (!$this->fileRepository->existsFileId($file_id)) {
            throw new \App\Exceptions\Evidence\FileNotFoundException();
        }
        $file = $this->fileRepository->getFile($file_id);

        $standard = $this->standardRepository->getStandardActiveById($file->standard_id);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($file->evidence_type_id);

        $pathRoot = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;

        $path = storage_path('app/' . $pathRoot . $file->path);
        return [
            'path' => $path,
            'name' => $file->file
        ];
    }

    public function renameFile(Request $request)
    {
        $year = $request->route('year');
        $semester = $request->route('semester');
        $file_id = $request->route('file_id');

        $newName = $request->new_filename;

        if (!$this->fileRepository->existsFileId($file_id)) {
            throw new \App\Exceptions\Evidence\FileNotFoundException();
        }
        $file = $this->fileRepository->getFile($file_id);

        $standard = $this->standardRepository->getStandardActiveById($file->standard_id);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($file->evidence_type_id);

        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;
        $oldPath = $file->path;
        
        $generalPath = '/';
        if ($file->folder_id) {
            $parentfolder = $this->folderRepository->getFolder($file->folder_id);
            $generalPath = $parentfolder->path;
        }
        $relativePath = $generalPath == '/' ? $generalPath . $newName : $generalPath . '/' . $newName;
        $generalPath = $generalPath == '/' ? '' : $generalPath;

        if ($this->fileRepository->existsFile($relativePath . '.pdf', $file->standard_id, $file->evidence_type_id, $file->date_id)) {
            throw new \App\Exceptions\Evidence\EvidenceAlreadyExistsException("Ya existe un archivo con este nombre: " . $newName);
        }

        $newPath = $this->getNewFilePath($file, $newName);

        if (Storage::exists($currentPath . $oldPath)) {
            Storage::move($currentPath . $oldPath, $currentPath . $newPath);
        }

        $file->name = $newName;
        $file->path = $newPath;
        $file->file = $newName . '.' . $file->type;
        $file->save();
        return $file;
    }

    private function getNewFilePath($file, $newName)
    {
        $pathParts = explode('/', $file->path);
        array_pop($pathParts);
        $pathParts[] = $newName . '.' . $file->type;
        return implode('/', $pathParts);
    }

    public function moveFile(Request $request)
    {
        $year = $request->route('year');
        $semester = $request->route('semester');
        $file_id = $request->route('file_id');
        $dateId = DateModel::dateId($year, $semester);

        if (!$this->fileRepository->existsFileId($file_id)) {
            throw new \App\Exceptions\Evidence\FileNotFoundException();
        }
        $file = $this->fileRepository->getFile($file_id);

        $parentFolder = $file->folder;
        $newParentId = $request->parent_id ? $request->parent_id : null;

        if ($parentFolder && $this->evidenceRepository->existsEvidenceFolder($parentFolder->standard_id, $parentFolder->evidence_type_id, $parentFolder->date_id, $parentFolder->id)) {
            throw new \App\Exceptions\Evidence\FolderCannotHaveEvidencesException("El archivo no puede moverse ya que se encuentra en un folder evidencia.");
        }
        if (
            $this->evidenceRepository->existsEvidenceFile($file->standard_id, $file->evidence_type_id, $file->date_id, $file->id)
            && $newParentId
            && $this->evidenceRepository->existsEvidenceFolder($file->standard_id, $file->evidence_type_id, $file->date_id, $newParentId)
        ) {
            throw new \App\Exceptions\Evidence\FolderCannotHaveEvidencesException("El archivo es una evidencia así que no puede moverse a un folder evidencia.");
        }

        if ($newParentId && !$this->folderRepository->exists($newParentId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }
        $newParentFolder = $newParentId ? $this->folderRepository->getFolder($newParentId) : null;
        

        if ($parentFolder && $parentFolder->id == $file->id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover el archivo a la misma carpeta.");
        }
        if ($parentFolder && $parentFolder->evidence_type_id != $file->evidence_type_id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover el archivo a una carpeta de otro tipo de evidencia");
        }
        if ($parentFolder && $parentFolder->date_id != $file->date_id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover el archivo a una carpeta de otro periodo");
        }
        if ($parentFolder && $parentFolder->standard_id != $file->standard_id) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException("No se puede mover el archivo a una carpeta de otro estándar");
        }
        $this->standardService->narrativeIsEnabled($file->standard_id);

        $standardId = $file->standard_id;
        $typeEvidenceId = $file->evidence_type_id;

        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $generalPath = '/';
        if ($file->folder_id) {
            $parentfolder = $this->folderRepository->getFolder($file->folder_id);
            $generalPath = $parentfolder->path;
        }
        $relativePath = $generalPath == '/' ? $generalPath . $file->file : $generalPath . '/' . $file->file;
        $generalPath = $generalPath == '/' ? '' : $generalPath;

        if ($this->fileRepository->existsFileInFolder($relativePath . '.pdf', $file->standard_id, $file->evidence_type_id, $file->date_id, $file->folder_id)) {
            throw new \App\Exceptions\Evidence\EvidenceAlreadyExistsException("Ya existe un archivo con el mismo nombre en la carpeta destino." );
        }


        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;
        $evidenceFile = $this->evidenceRepository->moveFile($file, $newParentFolder, $currentPath);

        return $evidenceFile;
    }


    public function deleteFile($year, $semester, $file_id)
    {
        $dateId = DateModel::dateId($year, $semester);


        if (!$this->fileRepository->existsFileId($file_id)) {
            throw new \App\Exceptions\Evidence\FileNotFoundException();
        }
        $file = $this->fileRepository->getFile($file_id);
        $this->standardService->narrativeIsEnabled($file->standard_id);
        $standardId = $file->standard_id;
        $typeEvidenceId = $file->evidence_type_id;

        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $currentPath = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;

        $result = $this->fileRepository->deleteFile($file->id, $dateId, $standardId, $typeEvidenceId, $currentPath);

        return $result;
    }

    public function createFileEvidence(Request $request)
    {
        $userId = auth()->user()->id;
        $year = $request->route('year');
        $semester = $request->route('semester');
        $dateId = DateModel::dateId($year, $semester);

        $standardId = $request->standard_id;
        $typeEvidenceId = $request->type_evidence_id;
        $planId = $request->has('plan_id') ? $request->plan_id : null;
        //$generalPath = $request->has('path') ? $request->path : '/';
        $folderId = $request->has('folder_id') ? $request->folder_id : null;
        $generalPath = '/';

        if (!$this->standardRepository->getStandardActiveById($standardId)) {
            throw new \App\Exceptions\Standard\StandardNotFoundException();
        }
        if ($folderId && !$this->folderRepository->exists($folderId)) {
            throw new \App\Exceptions\Evidence\FolderNotFoundException();
        }
        if (!$this->evidenceRepository->existsTypeEvidence($typeEvidenceId)) {
            throw new \App\Exceptions\Evidence\EvidenceTypeNotFoundException();
        }

        $folder = null;
        if ($folderId) {
            $folder = $this->folderRepository->getFolder($folderId);
            $generalPath = $folder->path;
        }
        $file = $request->file('file');
        $standard = $this->standardRepository->getStandardActiveById($standardId);
        $evidence_type = $this->evidenceRepository->getTypeEvidence($typeEvidenceId);

        $pathRoot = 'evidencias/' . $year . '/' . $semester . '/' . 'estandar_' . $standard->nro_standard . '/tipo_evidencia_' . $evidence_type->description;
        $relativePath = $generalPath == '/' ? $generalPath . $file->getClientOriginalName() : $generalPath . '/' . $file->getClientOriginalName();
        $generalPath = $generalPath == '/' ? '' : $generalPath;

        if ($this->fileRepository->existsFile($relativePath, $standardId, $typeEvidenceId, $dateId)) {
            throw new \App\Exceptions\Evidence\EvidenceAlreadyExistsException();
        }
        if ($planId && !$this->planRepository->getPlanActiveById($planId)) {
            throw new \App\Exceptions\Plan\PlanNotFoundException();
        }
        $evidenceFile = null;
        $folder_is_evidence = $folderId ? $this->evidenceRepository->existsEvidenceFolder($standard->id, $evidence_type->id, $dateId, $folderId) : null;
        if ($folder_is_evidence || $planId) {
            $evidenceFile = $this->fileRepository->createFile(
                pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                $relativePath,
                $file->getClientOriginalName(),
                $file->getClientOriginalExtension(),
                $file->getSize(),
                $userId,
                $planId,
                $folder ? $folder->id : null,
                $typeEvidenceId,
                $standardId,
                $dateId
            );
        } else {
            $evidenceFile = $this->evidenceRepository->createFileEvidence(
                pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                $relativePath,
                $file->getClientOriginalName(),
                $file->getClientOriginalExtension(),
                $file->getSize(),
                $userId,
                $planId,
                $folder ? $folder->id : null,
                $typeEvidenceId,
                $standardId,
                $dateId
            );
        }

        $path = $file->storeAs($pathRoot . $generalPath, $file->getClientOriginalName());

        return $evidenceFile;
    }

    public function reportAllEvidences(Request $request)
    {
        $tempfiledocx = tempnam(sys_get_temp_dir(), 'PHPWord');
        $template = new \PhpOffice\PhpWord\TemplateProcessor('plantilla-evidencias.docx');
        
        //Rango de periodos
        $startYear = $request->input('startYear');
        $startSemester = $request->input('startSemester');
        $endYear = $request->input('endYear');
        $endSemester = $request->input('endSemester');
        //Comprobaciones
        if($startYear>$endYear){
            $temp = $startYear;
            $startYear = $endYear;
            $endYear = $temp;

            $tempSemester = $startSemester;
            $startSemester  = $endSemester;
            $endSemester = $tempSemester;
        }
        else if($startYear == $endYear && $startSemester == 'B'){
            $tempSemester = $startSemester;
            $startSemester  = $endSemester;
            $endSemester = $tempSemester;
        }
        $dates = $this->dateRepository->getDatesByRange($startYear, $startSemester, $endYear, $endSemester);

        $standards = StandardModel::where("date_id", 1)->orderBy('nro_standard')->get();
        if ($standards->count() > 0) {
            $template->cloneBlock('block_periodo', $dates->count(), true, true);
            $template->cloneBlock('block_estandar', $standards->count(), true, true);

            foreach ($standards as $key => $standard) {
                // Dimensión
                $template->setValue('dimension#' . ($key + 1), $standard->dimension);
                // Factor
                $template->setValue('factor#' . ($key + 1), $standard->factor);
                // Estandar
                $template->setValue('n-e#' . ($key + 1), $standard->nro_standard);
                $template->setValue('estandar#' . ($key + 1), $standard->name);


                //Periodos
                foreach ($dates as $j => $date) {
                    $template->setValue('year#' . ($j + 1) . '#' . ($key + 1), $date->year);
                    $template->setValue('semester#' . ($j + 1) . '#' . ($key + 1), $date->semester);
                    $evidencesCount = EvidenceModel::where("standard_id", $standard->id)->where("date_id", $date->id)->count();
                    if ($evidencesCount > 0) {
                        $template->cloneRow('n#' . ($j + 1) . '#' . ($key + 1), $evidencesCount);
                        $evidencias = EvidenceModel::where("standard_id", $standard->id)->where("date_id", $date->id)->get();
                        foreach ($evidencias as $m => $evidence) {
                            $template->setValue('n#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), ($m + 1));
                            $template->setValue('codigo#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), $evidence->code);
                            $template->setValue('nombre#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), getName($evidence));
                            $template->setValue('tipo#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), EvidenceTypeModel::evidenceId($evidence->evidence_type_id));
                            $template->setValue('tamaño#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), getSize($evidence));
                            $template->setValue('fecha#' . ($j + 1) . "#" . ($key + 1) . "#" . ($m + 1), $evidence->created_at->toDateString());
                        }
                    } else {
                        $template->cloneRow('n#' . ($j + 1) . '#' . ($key + 1), -1);
                        $template->setValue("block_tabla#" . ($j + 1) . "#" . ($key + 1), "No hay evidencias");
                    }
                }
            }

            $template->saveAs($tempfiledocx);
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment;filename="evidencias.docx"',
            ];
            return response()->download($tempfiledocx, "reporte_evidencias_{$startYear}-{$startSemester}_{$endYear}-{$endSemester}.docx", $headers);
        } else {
            return response([
                "message" => "!No cuenta con ningún estándar todavía en este periodo",
            ], 404);
        }
    }

    public function getName($evidence){
        if($evidence->folder_id){
            return $evidence->file()->name;
        }else{
            return $evidence->folder()->name;
        }        
    }

    public function getSize($evidence){
        if($evidence->folder_id){
            return $evidence->file()->size;
        }else{
            return $evidence->folder()->files();
        }        
    }
}
