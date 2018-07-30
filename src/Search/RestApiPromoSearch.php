<?php

namespace Messere\PromoAudioScrapper\Search;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class RestApiPromoSearch implements IPromoSearch
{
    private const PROMO_SEARCH_ENDPOINT =
        'https://slide.ly/promoVideos/data/search-promo-collection?'
        . 'keyword={keyword}&page=1&sort_order=most_popular&limit=1';

    private $guzzleClient;

    public function __construct(ClientInterface $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param string $keyword
     * @return SearchResult|null
     * @throws SearchException
     */
    public function searchVideo(string $keyword): ?SearchResult
    {
        try {
            $response = $this->guzzleClient->request(
                'GET',
                $this->prepareUrl(static::PROMO_SEARCH_ENDPOINT, $keyword)
            );

            $this->validateResponse($response);
            $decodedResponse = $this->decodeResponse($response->getBody()->getContents());
            return $this->extractInformationFrom($decodedResponse);
        } catch (GuzzleException $e) {
            throw new SearchException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function prepareUrl(string $urlTemplate, string $keyword): string
    {
        return str_replace('{keyword}', urlencode($keyword), $urlTemplate);
    }

    /**
     * @param ResponseInterface $response
     * @throws SearchException
     */
    private function validateResponse(ResponseInterface $response): void
    {
        if ($response->getStatusCode() !== 200) {
            throw new SearchException('Search API error');
        }

        $contentType = $response->getHeaderLine('content-type');
        if (false === stripos($contentType, 'application/json')) {
            throw new SearchException('Unexpected response type from search API: ' . $contentType);
        }
    }

    private function decodeResponse(string $contents): array
    {
        $decodedContents = json_decode($contents, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SearchException(
                'Invalid Search API response, cannot decode JSON: ' . json_last_error_msg()
            );
        }
        return $decodedContents;
    }

    private function extractInformationFrom(array $decodedResponse): ?SearchResult
    {
        $version = $decodedResponse['response_version'] ?? '';
        if (2 !== $version) {
            throw new SearchException('Unsupported Search API response version: ' . $version);
        }

        $status = $decodedResponse['status'] ?? '';
        if ('OK' !== $status) {
            throw new SearchException(
                'Unsuccessful Search API call ' . ($decodedResponse['status_text'] ?? '')
            );
        }

        $videos = $decodedResponse['response']['body']['videos'] ?? [];
        if (!\is_array($videos)) {
            throw new SearchException('Unexpected Search API response, cannot find videos');
        }

        if (\count($videos) === 0) {
            return null;
        }

        $videoId = $videos[0]['videoId'] ?? null;
        $url = $videos[0]['previewUrl'] ?? null;

        if ($videoId === null || $url === null) {
            throw new SearchException(
                'Unexpected Search API response, cannot find required videoId and previewUrl'
            );
        }

        return new SearchResult($url, $videoId);
    }
}
