<?php

namespace Messere\PromoAudioScrapper\Service;

use Messere\PromoAudioScrapper\Audio\AudioExtractionException;
use Messere\PromoAudioScrapper\Audio\IAudioExtractor;
use Messere\PromoAudioScrapper\FileStorage\IFileStorage;
use Messere\PromoAudioScrapper\FileStorage\PublicFileLocation;
use Messere\PromoAudioScrapper\Search\IPromoSearch;
use Messere\PromoAudioScrapper\Search\SearchException;
use Messere\PromoAudioScrapper\Search\SearchResult;
use PHPUnit\Framework\TestCase;

class Promo2Mp3ServiceTest extends TestCase
{
    private $search;
    private $extractor;
    private $storage;
    private $service;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->search = $this->prophesize(IPromoSearch::class);
        $this->extractor = $this->prophesize(IAudioExtractor::class);

        $this->storage = $this->prophesize(IFileStorage::class);
        $this->storage->generateLocation('.mp3')->willReturn(
            new PublicFileLocation('path', 'mp3url')
        );

        $this->service = new Promo2Mp3Service(
            $this->search->reveal(),
            $this->extractor->reveal(),
            $this->storage->reveal()
        );
    }

    public function testSuccessfulExtraction(): void
    {
        $this->search->searchVideo('fun')->willReturn(
            new SearchResult('url', 'id')
        );
        $this->extractor
            ->extractAudio('url', 'mp3', 'path')
            ->shouldBeCalledTimes(1);
        $result = $this->service->searchAndExtract('fun');
        $this->assertEquals('id', $result->getVideoId());
        $this->assertEquals('mp3url', $result->getDownloadUrl());
    }

    /**
     * @expectedException \Messere\PromoAudioScrapper\Service\ExtractionException
     */
    public function testWhenThereIsNoSearchResults(): void
    {
        $this->search->searchVideo('fun')->willReturn(null);
        $this->service->searchAndExtract('fun');
    }

    /**
     * @expectedException \Messere\PromoAudioScrapper\Service\ExtractionException
     */
    public function testWhenAudioExtractorFails(): void
    {
        $this->search->searchVideo('fun')->willReturn(
            new SearchResult('url', 'id')
        );
        $this->extractor
            ->extractAudio('url', 'mp3', 'path')
            ->willThrow(new AudioExtractionException());
        $this->service->searchAndExtract('fun');
    }

    /**
     * @expectedException \Messere\PromoAudioScrapper\Service\ExtractionException
     */
    public function testWhenSearchFails(): void
    {
        $this->search->searchVideo('fun')->willThrow(
            new SearchException()
        );
        $this->service->searchAndExtract('fun');
    }
}
