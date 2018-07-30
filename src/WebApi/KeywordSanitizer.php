<?php

namespace Messere\PromoAudioScrapper\WebApi;

use Slim\Http\Request;

class KeywordSanitizer
{
    /**
     * @param Request $request
     * @return string
     * @throws ValidationException
     */
    public function getKeywordFromRequest(Request $request): string
    {
        $keyword = $request->getQueryParam('tag', '');

        if (!\is_string($keyword)) {
            throw new ValidationException('Invalid request: missing or invalid tag query parameter');
        }

        $keyword = trim($keyword);

        if ($keyword === '') {
            throw new ValidationException('Invalid request: missing or invalid tag query parameter');
        }

        return $keyword;
    }
}
