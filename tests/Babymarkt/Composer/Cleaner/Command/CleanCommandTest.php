<?php

namespace Babymarkt\Composer\Cleaner\Command;

use Babymarkt\Composer\Cleaner\Cleaner;
use Babymarkt\Composer\Cleaner\Context;
use Babymarkt\Composer\Cleaner\GlobTester;
use Composer\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

require_once './tests/fixtures/function.glob.php';

class CleanCommandTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        GlobTester::$callback = null;
    }

    protected function tearDown()
    {
        GlobTester::$callback = null;
    }

    public function testDryRun()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Composer\Util\Filesystem $fileSystem */
        $fileSystem = $this->getMockBuilder(\Composer\Util\Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(array('normalizePath', 'remove'))
            ->getMock();

        $fileSystem->method('normalizePath')->willReturnArgument(0);

        // This method is not expected to be called because of the enabled dry-mode.
        $fileSystem->expects($this->never())->method('remove');

        GlobTester::$callback = function () {
            return array('file-to-delete');
        };

        $this->cleaner = new Cleaner($fileSystem, array(
            new Context(array('pattern' => '*'), 'test')
        ));

        $command = new CleanCommand();
        $command->setApplication(new Application());
        $command->setCleaner($this->cleaner);

        $tester = new CommandTester($command);
        $tester->execute(['--dry-run' => true, 'context' => 'test']);
        $output = $tester->getDisplay();

        $this->assertContains('dry-run mode enabled', $output);
        $this->assertContains('file-to-delete ... OK', $output);
        $this->assertEquals(0, $tester->getStatusCode());
    }

    public function testFailedRemovingFile() {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Composer\Util\Filesystem $fileSystem */
        $fileSystem = $this->getMockBuilder(\Composer\Util\Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(array('normalizePath', 'remove'))
            ->getMock();

        $fileSystem->method('normalizePath')->willReturnArgument(0);
        $fileSystem->method('remove')->willReturn(false);

        GlobTester::$callback = function () {
            return array('file-to-delete');
        };

        $this->cleaner = new Cleaner($fileSystem, array(
            new Context(array('pattern' => '*'), 'test')
        ));

        $command = new CleanCommand();
        $command->setApplication(new Application());
        $command->setCleaner($this->cleaner);

        $tester = new CommandTester($command);
        $tester->execute(['context' => 'test']);
        $output = $tester->getDisplay();

        $this->assertNotContains('dry-run mode enabled', $output);
        $this->assertContains('file-to-delete ... FAILED', $output);
        $this->assertNotEquals(0, $tester->getStatusCode());
    }

}
