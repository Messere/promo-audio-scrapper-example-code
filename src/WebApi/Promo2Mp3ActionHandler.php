<?php

namespace Messere\PromoAudioScrapper\WebApi;

use Messere\PromoAudioScrapper\Service\ExtractionException;
use Messere\PromoAudioScrapper\Service\FatalExtractionException;
use Messere\PromoAudioScrapper\Service\Promo2Mp3Service;
use Slim\Http\Request;
use Slim\Http\Response;

class Promo2Mp3ActionHandler
{
    private $promo2mp3Service;
    private $keywordSanitizer;

    public function __construct(
        Promo2Mp3Service $promo2mp3Service,
        KeywordSanitizer $keywordSanitizer
    ) {
        $this->promo2mp3Service = $promo2mp3Service;
        $this->keywordSanitizer = $keywordSanitizer;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $keyword = $this->keywordSanitizer->getKeywordFromRequest($request);
        } catch (ValidationException $e) {
            return $response->withStatus(400, $e->getMessage());
        }
        try {
            $result = $this->promo2mp3Service->searchAndExtract($keyword);
            $status = 200;
        } catch (FatalExtractionException $e) {
            $result = $e;
            $status = 500;
        } catch (ExtractionException $e) {
            $result = $e;
            $status = 200;
        }
        return $response->withStatus($status)->withJson(
            $result->jsonSerialize()
        );
    }
}
