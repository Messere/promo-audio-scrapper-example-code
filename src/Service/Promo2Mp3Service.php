<?php

namespace Messere\PromoAudioScrapper\Service;

use Messere\PromoAudioScrapper\Audio\AudioExtractionException;
use Messere\PromoAudioScrapper\Audio\IAudioExtractor;
use Messere\PromoAudioScrapper\FileStorage\IFileStorage;
use Messere\PromoAudioScrapper\Search\IPromoSearch;
use Messere\PromoAudioScrapper\Search\SearchException;

class Promo2Mp3Service
{
    private const AUDIO_FORMAT = 'mp3';

    private $promoSearch;
    private $audioExtractor;
    private $fileStorage;

    public function __construct(
        IPromoSearch $promoSearch,
        IAudioExtractor $audioExtractor,
        IFileStorage $fileStorage
    ) {
        $this->promoSearch = $promoSearch;
        $this->audioExtractor = $audioExtractor;
        $this->fileStorage = $fileStorage;
    }

    public function searchAndExtract(string $keyword): ExtractionResult
    {
        try {
            $publicFileLocation = $this->fileStorage->generateLocation('.' . static::AUDIO_FORMAT);
            $searchResult = $this->promoSearch->searchVideo($keyword);

            if (null === $searchResult) {
                throw new ExtractionException('No results for keyword: ' . $keyword);
            }

            $this->audioExtractor->extractAudio(
                $searchResult->getVideoUrl(),
                static::AUDIO_FORMAT,
                $publicFileLocation->getFileSystemPath()
            );
        } catch (AudioExtractionException|SearchException $e) {
            throw new FatalExtractionException($e->getMessage());
        }

        return new ExtractionResult(
            $searchResult->getVideoId(),
            $publicFileLocation->getPublicUrl()
        );
    }
}
