<?php

namespace Messere\PromoAudioScrapper\ShellCommand;

interface ICommandExecutor
{
    public function execute(string $command): CommandResult;
}
