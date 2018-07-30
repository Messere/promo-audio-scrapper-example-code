<?php

namespace Messere\PromoAudioScrapper\ShellCommand;

use PHPUnit\Framework\TestCase;

class ExecCommandExecutorTest extends TestCase
{
    public function testExecute()
    {
        $executor = new ExecCommandExecutor();
        $this->assertEquals(
            new CommandResult(0, 'a'),
            $executor->execute('echo a')
        );
    }
}
