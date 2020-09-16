<?php

namespace Babymarkt\Composer\Cleaner;

use Cleaner\CleanerTest;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Util\Filesystem;

class CleanerPlugin implements PluginInterface, CommandProvider, Capable
{
    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    protected static $defaultConfig = array(
        'cleaner' => array(
            'default' => array(
                // Default pattern to clean up.
                'pattern' => array(
                    'README*',
                    'CHANGELOG*',
                    'FAQ*',
                    'CONTRIBUTING*',
                    'HISTORY*',
                    'UPGRADING*',
                    'UPGRADE*',
                    'package*',
                    'demo',
                    'example',
                    'examples',
                    'doc',
                    'docs',
                    'readme*',
                    'changelog*',
                    'composer*',
                    '.travis.yml',
                    '.scrutinizer.yml',
                    'phpcs.xml*',
                    'phpcs.php',
                    'phpunit.xml*',
                    'phpunit.php',
                    'test',
                    'tests',
                    'Tests',
                    'travis',
                    'patchwork.json'
                ),
                // Pattern to be excluded from clean up
                "exclude" => array()
            )
        )
    );

    /**
     * Activates the plugin.
     * @param Composer $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
    }

    /**
     * A Cleaner factory.
     * @return Cleaner
     */
    protected function createCleanerInstance()
    {
        $config = array_replace_recursive(
            self::$defaultConfig,
            $this->composer->getPackage()->getExtra()
        );

        return new Cleaner(new Filesystem(), $config['cleaner']);
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {

    }

    public function uninstall(Composer $composer, IOInterface $io)
    {

    }

    /**
     * Returns a list of available commands.
     * @return CleanerCommand[]|\Composer\Command\BaseCommand[]
     */
    public function getCommands()
    {
        $cleanerCommand = new CleanerCommand();
        $cleanerCommand->setCleaner($this->createCleanerInstance());

        return array($cleanerCommand);
    }

    /**
     * Returns a list of capabilities.
     * @return string[]
     */
    public function getCapabilities()
    {
        return array(
            CommandProvider::class => self::class
        );
    }
}