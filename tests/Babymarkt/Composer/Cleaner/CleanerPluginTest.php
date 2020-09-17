<?php

namespace Babymarkt\Composer\Cleaner;

class CleanerPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    protected $plugin;

    protected function setUp()
    {
        $this->plugin = new Plugin();
    }

    public function testTrue()
    {
        $this->assertTrue(true);
    }
}
