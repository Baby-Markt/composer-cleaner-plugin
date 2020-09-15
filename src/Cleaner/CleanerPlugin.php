<?php

namespace Babymarkt\Composer\Cleaner;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\PluginInterface;
use Composer\Util\Filesystem;

class CleanerPlugin implements PluginInterface, CommandProvider
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

    protected static $defaultConfig = [
        'clean' => [
            'default' => []
        ]
    ];

    public static function clean(Event $event)
    {
        $config = array_replace_recursive(
            self::$defaultConfig,
            $event->getComposer()->getPackage()->getExtra()
        );

        $cleaner = new Cleaner(new Filesystem(), $config['clean']);

        $args = $event->getArguments();

        $cleaner->run(getcwd(), isset($args[0]) ? $args[0] : 'default');
    }

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
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
}