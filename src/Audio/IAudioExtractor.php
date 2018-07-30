<?php

namespace Messere\PromoAudioScrapper\Audio;

interface IAudioExtractor
{
    /**
     * @param string $videoUrl
     * @param string $audioFormat
     * @param string $destinationFile
     * @throws AudioExtractionException
     */
    public function extractAudio(string $videoUrl, string $audioFormat, string $destinationFile): void;
}
