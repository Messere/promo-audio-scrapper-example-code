<?php

namespace Messere\PromoAudioScrapper\Audio;

use Messere\PromoAudioScrapper\ShellCommand\CommandResult;
use Messere\PromoAudioScrapper\ShellCommand\ICommandExecutor;
use PHPUnit\Framework\TestCase;

class FfmpegAudioExtractorTest extends TestCase
{
    private $executor;
    private $extractor;

    public function setUp(): void
    {
        parent::setUp();

        $this->executor = $this->prophesize(ICommandExecutor::class);
        $this->extractor = new FfmpegAudioExtractor($this->executor->reveal(), 'fff');
    }

    public function testShouldExecuteFfmpeg(): void
    {
        $this->executor->execute(
            'fff -v quiet -i "url" -map 0:1 -codec aac "file.aac"'
        )->shouldBeCalledTimes(1)->willReturn(
            new CommandResult(0, '')
        );
        $this->extractor->extractAudio('url', 'aac', 'file.aac');
    }

    /**
     * @expectedException \Messere\PromoAudioScrapper\Audio\AudioExtractionException
     */
    public function testShouldThrowExceptionIfFfmpegFails(): void
    {
        $this->executor->execute(
            'fff -v quiet -i "url" -map 0:1 -codec aac "file.aac"'
        )->shouldBeCalledTimes(1)->willReturn(
            new CommandResult(1, 'fail')
        );
        $this->extractor->extractAudio('url', 'aac', 'file.aac');
    }
}
