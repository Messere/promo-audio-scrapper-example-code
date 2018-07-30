<?php

namespace Messere\PromoAudioScrapper\ShellCommand;

class ExecCommandExecutor implements ICommandExecutor
{
    public function execute(string $command): CommandResult
    {
        $output = [];
        $returnStatus = 0;
        exec($command, $output, $returnStatus);

        $stringOutput = implode(PHP_EOL, $output);

        return new CommandResult($returnStatus, $stringOutput);
    }
}
