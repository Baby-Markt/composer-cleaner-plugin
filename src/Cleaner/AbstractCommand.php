<?php


namespace Babymarkt\Composer\Cleaner;


use Composer\Command\BaseCommand;
use Composer\Util\Filesystem;
use Composer\Util\ProcessExecutor;

abstract class AbstractCommand extends BaseCommand
{
    const EXTRA_CONFIG_KEY = 'babymarkt:cleaner';

    /**
     * @var Cleaner
     */
    private $cleaner;

    protected static $defaultConfig = array(
        self::EXTRA_CONFIG_KEY => array(
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
                // The paths to search in.
                "paths"   => array(),
                // Pattern to be excluded from clean up
                "exclude" => array()
            )
        )
    );

    /**
     * A Cleaner factory.
     * @return Cleaner
     */
    protected function getCleaner()
    {
        if ($this->cleaner === null) {
            $config = array_replace_recursive(
                self::$defaultConfig,
                $this->getComposer()->getPackage()->getExtra()
            );

            $executor   = new ProcessExecutor($this->getIO());
            $filesystem = new Filesystem($executor);

            $this->cleaner = new Cleaner($filesystem, $this->buildContexts($config[self::EXTRA_CONFIG_KEY]));
        }

        return $this->cleaner;
    }

    /**
     * @param array $config
     * @return array|Context[]
     */
    protected function buildContexts(array $config)
    {
        $contexts = array();
        foreach ($config as $name => $values) {
            $contexts[] = new Context($values, $name);
        }
        return $contexts;
    }
}