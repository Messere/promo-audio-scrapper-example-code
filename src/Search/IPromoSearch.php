<?php

namespace Messere\PromoAudioScrapper\Search;

interface IPromoSearch
{
    /**
     * @param string $keyword
     * @return SearchResult|null
     * @throws SearchException
     */
    public function searchVideo(string $keyword): ?SearchResult;
}
