<?php

namespace App\Services;

use Google\Client;
use Google\Service\Docs;
use Google\Service\Drive;

class GoogleDriveService
{
  protected $client;
  public function __construct()
  {
    $this->client = new Client();
    $this->client->setAuthConfig(base_path('credentials.json'));
    $this->client->addScope('https://www.googleapis.com/auth/drive');
  }

  public function createFolder($path)
  {
    $service = new Drive($this->client);
    $folder = explode('/', $path);
    $parentId = env('GOOGLE_DRIVE_FOLDER_ID');

    foreach ($folder as $folderName) {
      $folderId = $this->getFolderId($service, $folderName, $parentId);
      if (!$folderId) {
        $fileMetadata = new Drive\DriveFile([
          'name' => $folderName,
          'mimeType' => 'application/vnd.google-apps.folder',
          'parents' => $parentId ? [$parentId] : []
        ]);

        $folder = $service->files->create($fileMetadata, [
          'fields' => 'id'
        ]);
        $folderId = $folder->id;
      }

      $parentId = $folderId;
    }
    return $parentId;
  }

  /*   public function shareFolder($service, $folderId, $emailAddress)
  {
    $permission = new Drive\Permission([
      'type' => 'user',
      'role' => 'writer',
      'emailAddress' => $emailAddress,
    ]);

    try {
      $service->permissions->create($folderId, $permission, [
        'fields' => 'id',
      ]);
    } catch (\Exception $e) {
      throw new \Exception("Error al compartir la carpeta: " . $e->getMessage());
    }
  } */

  protected function getFolderId($service, $folderName, $parentId)
  {
    $query = "name = '$folderName' and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
    if ($parentId) {
      $query .= " and '$parentId' in parents";
    }

    $response = $service->files->listFiles([
      'q' => $query,
      'fields' => 'files(id, name)'
    ]);

    if (count($response->files) > 0) {
      return $response->files[0]->id;
    }

    return null;
  }

  public function createGoogleDoc($name, $parent_id)
  {
    $service = new Drive($this->client);

    $fileMetadata = new Drive\DriveFile([
      'name' => $name,
      'mimeType' => 'application/vnd.google-apps.document',
      'parents' => [$parent_id]
    ]);

    $doc = $service->files->create($fileMetadata, [
      'fields' => 'id'
    ]);

    return $doc->id;
  }

  public function shareGoogleDoc($docId, $emailAddress)
  {
    $service = new Drive($this->client);
    $permission = new Drive\Permission([
      'type' => 'user',
      'role' => 'writer',
      'emailAddress' => $emailAddress,
    ]);

    try {
      $service->permissions->create($docId, $permission, [
        'fields' => 'id',
      ]);
    } catch (\Exception $e) {
      // Manejo de errores
      throw new \Exception("Error al compartir el documento: " . $e->getMessage());
    }
  }

  public function writeToDoc($docId, $text, $link)
  {
    $docsService = new Docs($this->client);

    $document = $docsService->documents->get($docId);
    $startIndex = $document->getBody()->getContent()[count($document->getBody()->getContent()) - 1]->getEndIndex() - 1;

    $requests = [
      new Docs\Request([
        'insertText' => [
          'endOfSegmentLocation' => [],
          'text' => $text,
        ]
      ]),
      new Docs\Request([
        'updateTextStyle' => [
          'range' => [
            'startIndex' => $startIndex,
            'endIndex' => $startIndex + strlen($text)
          ],
          'textStyle' => [
            'link' => ['url' => $link],
            'bold' => true
          ],
          'fields' => 'link,bold'
        ]
      ])
    ];

    try {
      $docsService->documents->batchUpdate($docId, new Docs\BatchUpdateDocumentRequest(['requests' => $requests]));
    } catch (\Exception $e) {
      throw new \Exception("Error al escribir en el documento: " . $e->getMessage());
    }
  }
}
