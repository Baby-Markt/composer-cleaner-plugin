<?php


namespace Babymarkt\Composer\Cleaner;

use Composer\Util\Filesystem;

/**
 *
 */
class Cleaner
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var int
     */
    protected $cleanCounter = 0;

    /**
     * Cleaner constructor.
     * @param Filesystem $filesystem
     * @param array $config
     */
    public function __construct(Filesystem $filesystem, array $config)
    {
        $this->filesystem = $filesystem;
        $this->config     = $config;
    }

    /**
     * @param $basePath
     * @param $context
     */
    public function run($basePath, $context)
    {
        $this->basePath = $basePath;

        if (!isset($this->config[$context])) {
            throw new \InvalidArgumentException(
                sprintf('Clean context "%s" not defined. Found: %s', $context, implode(', ', array_keys($this->config)))
            );
        }

        foreach ($this->config[$context] as $globPattern) {
            $this->remove($globPattern);
        }
    }

    /**
     * Removes all files an directories recursively.
     * @param $pattern
     */
    protected function remove($pattern)
    {
        $matches = glob($pattern);
        foreach ($matches as $path) {
            if (is_dir($path)) {
                $this->remove($path . DIRECTORY_SEPARATOR . '*');
                echo "DELETE DIRECTORY: " . $path . PHP_EOL;
                if (rmdir($path)) {
                    $this->cleanCounter++;
                }
            } else {
                echo "DELETE FILE " . $path . PHP_EOL;
                if (unlink($path)) {
                    $this->cleanCounter++;
                }
            }
        }
    }

}