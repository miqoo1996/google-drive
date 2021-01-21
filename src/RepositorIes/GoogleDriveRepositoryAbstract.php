<?php

namespace Miqoo1996\GDrive\Repositories;

use Miqoo1996\GDrive\Services\GoogleClientDecorator;

abstract class GoogleDriveRepositoryAbstract
{
    protected $client;

    protected $service;


    /**
     * @var \Google_Service_Drive_FileList $fileList
     */
    protected $fileList;

    protected $optParams = [
        'pageSize' => 300,
        'fields' => "nextPageToken, files(contentHints/thumbnail,fileExtension,iconLink,id,name,size,thumbnailLink,webContentLink,webViewLink,mimeType)",
        'q' => "'root' in parents and trashed=false"
    ];

    abstract protected function setCredentials();

    public function __construct()
    {
        $this->client = new GoogleClientDecorator();

        $this->setCredentials();

        $this->service = new \Google_Service_Drive($this->client);

        $this->fileList = new \Google_Service_Drive_FileList();
    }

    public function __set($name, $value)
    {
        $this->optParams[$name] = $value;
    }

    /**
     * @return GoogleClientDecorator
     */
    public function getClient(): GoogleClientDecorator
    {
        return $this->client;
    }

    /**
     * @return \Google_Service_Drive
     */
    public function getService(): \Google_Service_Drive
    {
        return $this->service;
    }

    /**
     * @return \Google_Service_Drive_FileList
     */
    public function getFileList(): \Google_Service_Drive_FileList
    {
        return $this->fileList;
    }

    /**
     * @return array
     */
    public function getOptParams(array $optParams = []): array
    {
        return array_merge($this->optParams, $optParams);
    }

    /**
     * @param array $optParams
     */
    public function setOptParams(array $optParams): void
    {
        $this->optParams = $this->getOptParams($optParams);
    }

    /**
     * @param $query
     * @param callable|null $callback
     * @return \Google_Service_Drive_FileList
     */
    public function search($query, callable $callback = null)
    {
        $this->fileList = $this->service->files->listFiles($this->getOptParams(['q' => $query]));

        if ($callback && count($this->fileList->getFiles()) > 0) {
            $line = 0;
            foreach ($this->fileList->getFiles() as $file) {
                $callback($file, ++$line);
            }
        }

        return $this->fileList;
    }
}