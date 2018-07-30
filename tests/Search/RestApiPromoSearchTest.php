<?php

namespace Messere\PromoAudioScrapper\Search;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RestApiPromoSearchTest extends TestCase
{
    private $client;
    private $search;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->prophesize(ClientInterface::class);
        $this->search = new RestApiPromoSearch($this->client->reveal());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSuccessfulResponse(): void
    {
        $this->client->request(
            'GET',
            Argument::containingString('fun')
        )->willReturn(
            new Response(
                200,
                [ 'content-type' => 'application/json' ],
                <<<'BODY'
                 { 
                    "response_version": 2, 
                    "status": "OK", 
                    "response": { 
                        "body": { 
                            "videos": [ 
                                { "videoId": "1", "previewUrl": "url" } 
                            ] 
                        } 
                     }
                 } 
BODY

            )
        );

        $result = $this->search->searchVideo('fun');

        $this->assertEquals('url', $result->getVideoUrl());
        $this->assertEquals('1', $result->getVideoId());
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testSuccessfulResponseWithNoResults(): void
    {
        $this->client->request(
            'GET',
            Argument::containingString('fun')
        )->willReturn(
            new Response(
                200,
                [ 'content-type' => 'application/json' ],
                <<<'BODY'
                 { 
                    "response_version": 2, 
                    "status": "OK", 
                    "response": { 
                        "body": { 
                            "videos": [ 
                            ] 
                        } 
                     }
                 } 
BODY

            )
        );

        $result = $this->search->searchVideo('fun');

        $this->assertNull($result);
    }


    /**
     * @dataProvider invalidResponsesProvider
     * @param $status
     * @param $contentType
     * @param $body
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @expectedException \Messere\PromoAudioScrapper\Search\SearchException
     */
    public function testInvalidResponse($status, $contentType, $body): void
    {
        $this->client->request(
            'GET',
            Argument::containingString('fun')
        )->willReturn(
            new Response(
                $status,
                [ 'content-type' => $contentType ],
                json_encode($body)
            )
        );

        $this->search->searchVideo('fun');
    }

    public function invalidResponsesProvider(): array
    {
        return [
            [500, '', ''],
            [200, 'text/plain', ''],
            [200, 'application/json', []],
            [200, 'application/json', [ 'response_version' => 1 ]],
            [200, 'application/json', [ 'response_version' => 2, 'status' => 'BAD' ]],
            [200, 'application/json', [
                'response_version' => 2,
                'status' => 'OK',
                'response' => [
                    'body' => [
                        'videos' => 'aaa'
                    ]
                ]
            ]],
            [200, 'application/json', [
                'response_version' => 2,
                'status' => 'OK',
                'response' => [
                    'body' => [
                        'videos' => [
                            [ 'videoId' => 'a' ]
                        ]
                    ]
                ]
            ]],
            [200, 'application/json', [
                'response_version' => 2,
                'status' => 'OK',
                'response' => [
                    'body' => [
                        'videos' => [
                            [ 'previewUrl' => 'a' ]
                        ]
                    ]
                ]
            ]],
        ];
    }

    /**
     * @expectedException \Messere\PromoAudioScrapper\Search\SearchException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUnparsableResponse(): void
    {
        $this->client->request(
            'GET',
            Argument::containingString('fun')
        )->willReturn(
            new Response(
                200,
                [ 'content-type' => 'application/json' ],
                '{'
            )
        );

        $this->search->searchVideo('fun');
    }

    /**
     * @expectedException \Messere\PromoAudioScrapper\Search\SearchException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testWhenGuzzleThrowsException(): void
    {
        $this->client->request(
            'GET',
            Argument::containingString('fun')
        )->willThrow(
            new TransferException()
        );

        $this->search->searchVideo('fun');
    }
}
