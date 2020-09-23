<?php

namespace Babymarkt\Composer\Cleaner;


use Composer\Console\Application;

class DummyCommand extends AbstractCommand
{
}

class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testCleanerFactory()
    {
        $command = new DummyCommand();
        $command->setApplication(new Application());
        $cleaner = $command->getCleaner();

        $this->assertInstanceOf(Cleaner::class, $cleaner);
    }
}
