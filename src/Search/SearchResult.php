<?php

namespace Messere\PromoAudioScrapper\Search;

class SearchResult
{
    private $videoUrl;
    private $videoId;

    public function __construct($videoUrl, $videoId)
    {
        $this->videoUrl = $videoUrl;
        $this->videoId = $videoId;
    }

    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    public function getVideoId()
    {
        return $this->videoId;
    }
}
