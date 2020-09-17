<?php

namespace Babymarkt\Composer\Cleaner;

/**
 * Global function glob mock
 * @param $pattern
 * @param null $flags
 */
function glob($pattern, $flags = null)
{
    return CleanerTest::$globReturnValue !== null
        ? CleanerTest::$globReturnValue
        : glob($pattern, $flags);
}

class CleanerTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    public static $globReturnValue;

    /**
     * @var Cleaner
     */
    protected $cleaner;

    protected function setUp()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Composer\Util\Filesystem $fileSystem */
        $fileSystem = $this->getMockBuilder(\Composer\Util\Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(array('normalizePath'))
            ->getMock();

        $fileSystem->method('normalizePath')->willReturnArgument(0);

        $this->cleaner = new Cleaner($fileSystem, array(
            new Context(array('pattern' => '*', 'exclude' => '^important'), 'test')
        ));
    }

    protected function tearDown()
    {
        self::$globReturnValue = null;
    }

    public function testCallback()
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(array('trigger'))
            ->getMock();

        self::$globReturnValue = array('file-to-delete');

        $mock->expects($this->exactly(count(self::$globReturnValue) * 2))
            ->method('trigger')
            ->with($this->equalTo('file-to-delete'), $this->isType('string'))
            ->will($this->returnValue(true));

        $this->cleaner->registerCallback(array($mock, 'trigger'));
        $this->cleaner->run('test');
    }

    public function testContextExcludes() {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(array('trigger'))
            ->getMock();

        self::$globReturnValue = array('important/file-to-delete');

        $mock->expects($this->never())
            ->method('trigger');

        $this->cleaner->registerCallback(array($mock, 'trigger'));
        $this->cleaner->run('test');
    }

}
