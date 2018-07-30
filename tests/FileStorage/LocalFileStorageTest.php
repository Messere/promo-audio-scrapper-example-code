<?php

namespace Messere\PromoAudioScrapper\FileStorage;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactoryInterface;
use Slim\Http\Request;
use Slim\Http\Uri;

class LocalFileStorageTest extends TestCase
{
    private $uuidFactory;
    private $storage;
    private $request;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->uuidFactory = $this->prophesize(UuidFactoryInterface::class);
        $this->uuidFactory->uuid4()->willReturn(new class {
            public function toString(): string
            {
                return 'uuid';
            }
        });

        $this->request = $this->prophesize(Request::class);
        $this->request->getUri()->willReturn(
            new Uri('https', 'my-host', 77)
        );

        $this->storage = new LocalFileStorage(
            $this->uuidFactory->reveal(),
            $this->request->reveal()
        );
    }

    /**
     * @throws \Exception
     */
    public function testShouldGenerateLocation(): void
    {
        $location = $this->storage->generateLocation('.aac');
        $this->assertStringEndsWith('/public/mp3/uuid.aac', $location->getFileSystemPath());
        $this->assertEquals('https://my-host:77/mp3/uuid.aac', $location->getPublicUrl());
    }
}
