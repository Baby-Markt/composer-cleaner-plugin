<?php

namespace Babymarkt\Composer\Cleaner;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, Capable
{
    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * Activates the plugin.
     * @param Composer $composer
     * @param IOInterface $io
     * @codeCoverageIgnore
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io       = $io;
    }

    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @codeCoverageIgnore
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {

    }

    /**
     * @param Composer $composer
     * @param IOInterface $io
     * @codeCoverageIgnore
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {

    }

    /**
     * Returns a list of capabilities.
     * @return string[]
     */
    public function getCapabilities()
    {
        return array(
            CommandProviderInterface::class => CommandProvider::class
        );
    }
}