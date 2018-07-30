<?php

namespace Messere\PromoAudioScrapper\Service;

use PHPUnit\Framework\TestCase;

class ExtractionResultTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $result = new ExtractionResult('1', 'url');
        $this->assertEquals([
            'videoId' => 1,
            'downloadUrl' => 'url'
        ], $result->jsonSerialize());
    }
}
