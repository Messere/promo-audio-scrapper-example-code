<?php

namespace Messere\PromoAudioScrapper\FileStorage;

use Ramsey\Uuid\UuidFactoryInterface;
use Slim\Http\Request;

class LocalFileStorage implements IFileStorage
{
    // better implementation would take those values from configuration
    // those are defaults that will work in any circumstances
    private const BASE_PATH = __DIR__ . '/../../public/mp3';
    private const BASE_URL = '/mp3';

    private $uuidFactory;
    /**
     * @var Request
     */
    private $request;

    public function __construct(UuidFactoryInterface $uuidFactory, Request $request)
    {
        $this->uuidFactory = $uuidFactory;
        $this->request = $request;
    }

    /**
     * @param string $fileExtensionWithDot
     * @return PublicFileLocation
     * @throws \Exception
     */
    public function generateLocation(string $fileExtensionWithDot): PublicFileLocation
    {
        // note - this will always generate unique file name
        // in real case scenario file name could be based on url
        // to avoid fetching the same file multiple times

        $fileName = $this->uuidFactory->uuid4()->toString() . $fileExtensionWithDot;
        $dir = static::BASE_PATH . '/' . $fileName;
        $url = $this->buildAbsoluteUrl(static::BASE_URL . '/' . $fileName);
        return new PublicFileLocation($dir, $url);
    }

    private function buildAbsoluteUrl(string $path): string
    {
        $uri = $this->request->getUri();

        $pageURL = $uri->getScheme();
        $pageURL .= '://' . $uri->getHost();

        $port = $uri->getPort();
        if ($port !== '80' && $port !== null) {
            $pageURL .= ':' . $port;
        }

        return $pageURL . $path;
    }
}
