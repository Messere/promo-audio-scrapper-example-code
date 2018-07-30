<?php

namespace Messere\PromoAudioScrapper\Audio;

use Messere\PromoAudioScrapper\ShellCommand\ICommandExecutor;

class FfmpegAudioExtractor implements IAudioExtractor
{
    private $ffmpegExecutable;
    private $commandExecutor;

    public function __construct(
        ICommandExecutor $commandExecutor,
        string $ffmpegExecutable = 'ffmpeg'
    ) {
        $this->ffmpegExecutable = $ffmpegExecutable;
        $this->commandExecutor = $commandExecutor;
    }

    /**
     * @param string $videoUrl
     * @param string $audioFormat
     * @param string $destinationFile
     * @throws AudioExtractionException
     */
    public function extractAudio(string $videoUrl, string $audioFormat, string $destinationFile): void
    {
        $command = $this->buildCommand($this->ffmpegExecutable, $videoUrl, $audioFormat, $destinationFile);
        $result = $this->commandExecutor->execute($command);

        if ($result->getReturnStatus() !== 0) {
            throw new AudioExtractionException(sprintf(
                'Audio extraction error %s',
                $result->getOutput()
            ));
        }
    }

    private function buildCommand(
        string $ffmpegExecutable,
        string $videoUrl,
        string $audioFormat,
        string $destinationFile
    ) {
        // assumptions: video has at least one audio stream and it is second stream in video
        // otherwise extraction will fail. More careful implementation would have to examine
        // video file to assess what streams are inside.
        return sprintf(
            '%s -v quiet -i "%s" -map 0:1 -codec %s "%s"',
            $ffmpegExecutable,
            $videoUrl,
            $audioFormat,
            $destinationFile
        );
    }
}
