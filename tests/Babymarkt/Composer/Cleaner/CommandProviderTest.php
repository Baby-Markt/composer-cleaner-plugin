<?php

namespace Babymarkt\Composer\Cleaner;

class CommandProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCommands()
    {
        $instance = new CommandProvider();
        $commands = $instance->getCommands();

        $this->assertGreaterThan(0, count($commands), 'No commands registered.');

        foreach ($commands as $command) {
            $this->assertInstanceOf(AbstractCommand::class, $command, 'Command doesn\'t extends AbstractCommand');
        }
    }
}
