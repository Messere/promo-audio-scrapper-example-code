<?php

namespace Messere\PromoAudioScrapper\FileStorage;

interface IFileStorage
{
    public function generateLocation(string $fileExtensionWithDot): PublicFileLocation;
}
