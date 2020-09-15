<?php

use Babymarkt\Composer\Cleaner\Cleaner;

class CleanerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Cleaner
     */
    protected $cleaner;

    protected function setUp()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|\Composer\Util\Filesystem $fileSystem */
        $fileSystem = $this->getMockBuilder(\Composer\Util\Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cleaner = new Cleaner($fileSystem, array());
    }

    public function testCleanerInstance()
    {
        $this->assertInstanceOf(Cleaner::class, $this->cleaner);
    }

}
