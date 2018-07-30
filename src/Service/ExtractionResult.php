<?php

namespace Messere\PromoAudioScrapper\Service;

class ExtractionResult implements \JsonSerializable
{
    private $videoId;
    private $downloadUrl;

    public function __construct(string $videoId, string $downloadUrl)
    {
        $this->videoId = $videoId;
        $this->downloadUrl = $downloadUrl;
    }

    public function getVideoId(): string
    {
        return $this->videoId;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    public function jsonSerialize(): array
    {
        return [
            'videoId' => $this->getVideoId(),
            'downloadUrl' => $this->getDownloadUrl(),
        ];
    }
}
