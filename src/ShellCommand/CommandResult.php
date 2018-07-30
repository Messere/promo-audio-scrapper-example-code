<?php

namespace Messere\PromoAudioScrapper\ShellCommand;

class CommandResult
{
    private $returnStatus;
    private $output;

    public function __construct($returnStatus, $output)
    {
        $this->returnStatus = $returnStatus;
        $this->output = $output;
    }

    public function getReturnStatus()
    {
        return $this->returnStatus;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
