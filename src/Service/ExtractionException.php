<?php

namespace Messere\PromoAudioScrapper\Service;

class ExtractionException extends \RuntimeException implements \JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            'error' => true,
            'message' => $this->getMessage(),
        ];
    }
}
