<?php


namespace Babymarkt\Composer\Cleaner;


use Babymarkt\Composer\Cleaner\Command\CleanCommand;
use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderInterface;
use Composer\Util\Filesystem;

class CommandProvider implements CommandProviderInterface
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
     * CleanerCommandProvider constructor.
     * @param array $args Possible keys are: composer, io, plugin
     * @see \Composer\Plugin\PluginManager::getPluginCapability for arguments
     */
    public function __construct($args)
    {
        $this->composer = $args['composer'];
        $this->io       = $args['io'];
    }

    public function getCommands()
    {
        $cleanerCommand = new CleanCommand();
        $cleanerCommand->setCleaner($this->createCleanerInstance());

        return array($cleanerCommand);
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
}