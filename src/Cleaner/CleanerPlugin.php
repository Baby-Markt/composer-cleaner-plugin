<?php

namespace Babymarkt\Composer\Cleaner;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Util\Filesystem;

class CleanerPlugin implements PluginInterface, CommandProvider, Capable
{
    /**
     * @var Cleaner
     */
    protected $cleaner;

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
                "exclude" => array(

                )
            )
        )
    );

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
        $this->cleaner  = $this->createCleanerInstance();
    }

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
        // TODO: Implement deactivate() method.
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
        // TODO: Implement uninstall() method.
    }

    public function getCommands()
    {
        $cleanerCommand = new CleanerCommand();
        $cleanerCommand->setCleaner($this->cleaner);

        return array($cleanerCommand);
    }

    public function getCapabilities()
    {
        return array(
            CommandProvider::class => CleanerPlugin::class
        );
    }
}