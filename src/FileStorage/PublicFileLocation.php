<?php

namespace Messere\PromoAudioScrapper\FileStorage;

class PublicFileLocation
{
    private $fileSystemPath;
    private $publicUrl;

    public function __construct($fileSystemPath, $publicUrl)
    {
        $this->fileSystemPath = $fileSystemPath;
        $this->publicUrl = $publicUrl;
    }


    public function getFileSystemPath(): string
    {
        return $this->fileSystemPath;
    }

    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }
}
