<?php

namespace Miqoo1996\GDrive\Services;

final class StorageService
{
    private $path;

    public function __invoke()
    {
        return $this->getPath();
    }

    public function setPath($tokenPath)
    {
        $this->path = $tokenPath;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function isStored()
    {
        return file_exists($this->getPath());
    }

    public function delete()
    {
        if ($this->isStored()) {
            return unlink($this->getPath());
        }

        return false;
    }

    public function newInstance()
    {
        return new static();
    }
}