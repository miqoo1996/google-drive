<?php

namespace Miqoo1996\GDrive\Repositories;

use Miqoo1996\GDrive\Services\MimeTypesService;

class GoogleDriveRepository extends GoogleDriveRepositoryAbstract
{
    protected function setCredentials()
    {
        $this->client->setClientId(env('GDrive_client_id'));
        $this->client->setClientSecret(env('GDrive_client_secret'));
        $this->client->setRedirectUri(env('GDrive_client_redirect_url'));
    }

    public function getFilesByFolder(string $folder, callable $callback = null, string $trashed = "false") : \Google_Service_Drive_FileList
    {
        return $this->search("'$folder' in parents and trashed=$trashed", $callback);
    }

    public function getRootFiles(callable $callback = null, string $trashed = "false")
    {
        return $this->getFilesByFolder('root', $callback, $trashed);
    }

    public function createFolder($parentId, $folderName) : \Google_Service_Drive_DriveFile
    {
        // Setting File Matadata
        $fileMetadata = new \Google_Service_Drive_DriveFile([
            'name' => $folderName,
            'parents' => [$parentId],
            'mimeType' => 'application/vnd.google-apps.folder'
        ]);

        // Creating Folder with given Matadata and asking for ID field as result
        $file = $this->service->files->create($fileMetadata, ['fields' => 'id']);

        return $file;
    }

    public function updateFolder($folderName, $id, $parentId = null) : \Google_Service_Drive_DriveFile
    {
        $options = [
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder'
        ];

        if ($parentId) {
            $options['parents'] = [$parentId];
        }

        // Setting File Matadata
        $fileMetadata = new \Google_Service_Drive_DriveFile($options);

        // Creating Folder with given Matadata and asking for ID field as result
        $file = $this->service->files->update($id, $fileMetadata, ['fields' => 'id']);

        return $file;
    }

    public function createFile($options) : \Google_Service_Drive_DriveFile
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile($options);

        // Creating Folder with given Matadata and asking for ID field as result
        $file = $this->service->files->create($fileMetadata, ['fields' => 'id']);

        return $file;
    }

    public function updateFile($id, $options)
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile($options);

        // Creating Folder with given Matadata and asking for ID field as result
        $file = $this->service->files->update($id, $fileMetadata, ['fields' => 'id']);

        return $file;
    }

    public function deleteFile($id) : \GuzzleHttp\Psr7\Request
    {
        return $this->service->files->delete($id);
    }

    public function uploadFile($parentId, string $filePath, $fileName = "none") : \Google_Service_Drive_DriveFile
    {
        if ($fileName=="none") {
            $fileName = end(explode('/', $filePath));
        }

        $fileMetadata = new \Google_Service_Drive_DriveFile([
            'name' => $fileName,
            'parents' => [$parentId]
        ]);

        $content = file_get_contents($filePath . '/' . $fileName);

        $file = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => mime_content_type($filePath . '/' . $fileName),
            'uploadType' => 'multipart',
            'fields' => 'id'
        ]);

        return $file;
    }

    public function downloadFile($id, $populateTo)
    {
        $content = $this->service->files->get($id, ['alt' => 'media']);

        $populateTo = $populateTo . '/' . $id . '.' . MimeTypesService::convert($content->getMimeType(), true);

        $outHandle = fopen($populateTo, "w+");

        // Until we have reached the EOF, read 1024 bytes at a time and write to the output file handle.
        while (!$content->getBody()->eof()) {
            fwrite($outHandle, $content->getBody()->read(1024));
        }

        // Close output file handle.
        fclose($outHandle);
    }
}