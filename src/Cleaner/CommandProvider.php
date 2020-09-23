<?php


namespace Babymarkt\Composer\Cleaner;

use Babymarkt\Composer\Cleaner\Command\CleanCommand;
use Babymarkt\Composer\Cleaner\Command\ListCommand;
use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;

/**
 * Simple command provider
 */
class CommandProvider implements CommandProviderInterface
{
    public function getCommands()
    {
        return array(new CleanCommand(), new ListCommand());
    }
}