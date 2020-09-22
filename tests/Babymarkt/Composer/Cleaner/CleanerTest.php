<?php

namespace Babymarkt\Composer\Cleaner;

/**
 * Global function glob mock
 * @see \glob()
 */
function glob($pattern, $flags = null)
{
    return CleanerTest::$globCallback !== null
        ? call_user_func(CleanerTest::$globCallback, $pattern, $flags)
        : \glob($pattern, $flags);
}

class CleanerTest extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $globCallback;

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
        self::$globCallback = null;
    }

    public function testCallbackWithPositivResult()
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(array('trigger'))
            ->getMock();

        self::$globCallback = function () {
            return array('file-to-delete');
        };

        $mock->expects($this->exactly(2))
            ->method('trigger')
            ->with($this->equalTo('file-to-delete'), $this->isType('string'))
            ->will($this->returnValue(true));

        $this->cleaner->registerCallback(array($mock, 'trigger'));
        $this->cleaner->run('test');
    }

    public function testCallbackWithNegativeResult()
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(array('trigger'))
            ->getMock();

        self::$globCallback = function () {
            return array('file-to-delete');
        };

        $mock->expects($this->exactly(1))
            ->method('trigger')
            ->with($this->equalTo('file-to-delete'), $this->isType('string'))
            ->will($this->returnValue(false));

        $this->cleaner->registerCallback(array($mock, 'trigger'));
        $this->cleaner->run('test');
    }

    public function testContextExcludes()
    {
        $mock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(array('trigger'))
            ->getMock();
        $mock->expects($this->never())
            ->method('trigger');

        self::$globCallback = function () {
            return array('important/file-to-delete');
        };

        $this->cleaner->registerCallback(array($mock, 'trigger'));
        $this->cleaner->run('test');
    }

    /**
     * Absolute paths must be transformed to relatives.
     */
    public function testAbsolutePathsTransformation()
    {
        $absolutePath = '/etc';
        $relativePath = './etc';

        self::$globCallback = function ($pattern) use ($relativePath) {
            $this->assertEquals($relativePath . '/*', $pattern);
            return array();
        };

        $this->cleaner->setContexts(array(
            new Context(array('pattern' => '*', 'paths' => array($absolutePath)), 'test')
        ));
        $this->cleaner->run('test');
    }

    public function testUnknownContext()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cleaner->run('lsdhvslkdfh');
    }

    /**
     * @dataProvider dataTriggers
     */
    public function testTriggers($returnValue, $contains, $containsNot)
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Composer\Util\Filesystem $fileSystem */
        $fileSystem = $this->getMockBuilder(\Composer\Util\Filesystem::class)
            ->disableOriginalConstructor()
            ->setMethods(array('normalizePath', 'remove'))
            ->getMock();

        $fileSystem->method('normalizePath')->willReturnArgument(0);
        $fileSystem->method('remove')->willReturn($returnValue);

        $capturedEvents = [];
        $callback       = function ($path, $event) use (&$capturedEvents) {
            $capturedEvents[] = $event;
        };

        self::$globCallback = function () {
            return array('file-to-delete');
        };

        $this->cleaner = new Cleaner($fileSystem, array(
            new Context(array('pattern' => '*'), 'test')
        ));
        $this->cleaner->registerCallback($callback);
        $this->cleaner->run('test');

        $this->assertEquals(Cleaner::EVENT_PRE_REMOVE, $capturedEvents[0]);
        $this->assertEquals($contains, $capturedEvents[1]);
        $this->assertNotContains($containsNot, $capturedEvents);
    }

    public function dataTriggers()
    {
        return array(
            array(false, Cleaner::EVENT_REMOVE_FAILED, Cleaner::EVENT_REMOVE_SUCCESSFUL),
            array(true, Cleaner::EVENT_REMOVE_SUCCESSFUL, Cleaner::EVENT_REMOVE_FAILED),
        );
    }
}
