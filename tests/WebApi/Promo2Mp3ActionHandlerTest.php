<?php

namespace Messere\PromoAudioScrapper\WebApi;

use Messere\PromoAudioScrapper\Service\ExtractionException;
use Messere\PromoAudioScrapper\Service\ExtractionResult;
use Messere\PromoAudioScrapper\Service\FatalExtractionException;
use Messere\PromoAudioScrapper\Service\Promo2Mp3Service;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Slim\Http\Request;
use Slim\Http\Response;

class Promo2Mp3ActionHandlerTest extends TestCase
{
    private $service;
    private $request;
    private $response;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = $this->prophesize(Promo2Mp3Service::class);
        $this->request = $this->prophesize(Request::class);
        $this->response = $this->prophesize(Response::class);
    }

    private function callHandler(): Response
    {
        return (new Promo2Mp3ActionHandler(
            $this->service->reveal(),
            new KeywordSanitizer()
        ))($this->request->reveal(), $this->response->reveal());
    }

    /**
     * @dataProvider invalidKeywordProvider
     * @param $keyword
     */
    public function testResponseWithMissingKeyword($keyword): void
    {
        $this->request->getQueryParam('tag', '')->willReturn($keyword);
        $this->response
            ->withStatus(400, 'Invalid request: missing or invalid tag query parameter')
            ->shouldBeCalled()
            ->willReturn($this->response->reveal());
        $this->callHandler();
    }

    public function invalidKeywordProvider(): array
    {
        return [
            [null],
            [''],
            [[]],
            ['   '],
        ];
    }

    public function testSuccessfulExtraction(): void
    {
        $this->service->searchAndExtract('fun')->willReturn(
            new ExtractionResult('1', 'url')
        );
        $this->request->getQueryParam('tag', '')->willReturn('fun');
        $this->response
            ->withStatus(200)
            ->shouldBeCalled()
            ->willReturn($this->response->reveal());
        $this->response
            ->withJson(Argument::type('array'))
            ->willReturn($this->response->reveal());
        $this->callHandler();
    }

    public function testFatalExtractionError(): void
    {
        $this->service->searchAndExtract('fun')->willThrow(
            new FatalExtractionException()
        );
        $this->request->getQueryParam('tag', '')->willReturn('fun');
        $this->response
            ->withStatus(500)
            ->shouldBeCalled()
            ->willReturn($this->response->reveal());
        $this->response
            ->withJson(Argument::type('array'))
            ->willReturn($this->response->reveal());
        $this->callHandler();
    }

    public function testExtractionError(): void
    {
        $this->service->searchAndExtract('fun')->willThrow(
            new ExtractionException()
        );
        $this->request->getQueryParam('tag', '')->willReturn('fun');
        $this->response
            ->withStatus(200)
            ->shouldBeCalled()
            ->willReturn($this->response->reveal());
        $this->response
            ->withJson(Argument::type('array'))
            ->willReturn($this->response->reveal());
        $this->callHandler();
    }
}
