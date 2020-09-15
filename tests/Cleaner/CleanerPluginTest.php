<?php

namespace Cleaner;

use Babymarkt\Composer\Cleaner\CleanerPlugin;

class CleanerPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CleanerPlugin
     */
    protected $plugin;

    protected function setUp()
    {
        $this->plugin = new CleanerPlugin();
    }
}
