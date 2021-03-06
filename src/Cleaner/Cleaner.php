<?php


namespace Babymarkt\Composer\Cleaner;

use Composer\Util\Filesystem;

/**
 *
 */
class Cleaner
{
    const EVENT_PRE_REMOVE = 'preRemove';
    const EVENT_REMOVE_SUCCESSFUL = 'successful';
    const EVENT_REMOVE_FAILED = 'failed';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array|Context[]
     */
    protected $contexts;

    /**
     * Current context.
     * @var Context
     */
    protected $current;

    /**
     * @var int
     */
    protected $cleanCounter = 0;

    /**
     * @var callable[]
     */
    protected $callbacks = [];

    /**
     * @var bool
     */
    protected $dryRun = false;

    /**
     * Cleaner constructor.
     * @param Filesystem $filesystem
     * @param array|Context[] $contexts
     */
    public function __construct(Filesystem $filesystem, array $contexts)
    {
        $this->filesystem = $filesystem;
        $this->setContexts($contexts);

    }

    /**
     * @param string $contextName
     */
    public function run($contextName)
    {
        $contextName = strtolower($contextName);
        if (!isset($this->contexts[$contextName])) {
            throw new \InvalidArgumentException(
                sprintf('Cleaner context "%s" not defined. Do you mean: %s', $contextName, implode(', ', array_keys($this->contexts)))
            );
        }

        $this->current = $this->contexts[$contextName];

        $paths = $this->getNormalizedPaths();

        foreach ($this->contexts[$contextName]->getPattern() as $globPattern) {
            foreach ($paths as $path) {
                $this->remove($path . DIRECTORY_SEPARATOR . $globPattern);
            }
        }
    }

    /**
     * Removes all files an directories recursively.
     * @param string $pattern
     */
    protected function remove($pattern)
    {
        $matches = glob($pattern);
        foreach ($matches as $path) {
            if (!$this->isExcluded($path)) {
                if ($this->triggerCallbacks($path, self::EVENT_PRE_REMOVE)) {
                    if ($this->dryRun || $this->filesystem->remove($path)) {
                        $this->triggerCallbacks($path, self::EVENT_REMOVE_SUCCESSFUL);
                    } else {
                        $this->triggerCallbacks($path, self::EVENT_REMOVE_FAILED);
                    }
                }
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function isExcluded($path)
    {
        foreach ($this->current->getExclude() as $pattern) {
            if (preg_match('#' . $pattern . '#is', $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the normalized paths from context.
     */
    protected function getNormalizedPaths()
    {
        $paths = count($this->current->getPaths())
            ? $this->current->getPaths()
            : array('.');

        foreach ($paths as &$path) {
            $path = $this->filesystem->normalizePath($path);

            if (strpos($path, DIRECTORY_SEPARATOR) === 0) {
                // Make path relative.
                $path = '.' . $path;
            }
        }

        return $paths;
    }

    /**
     * Registers a new callback.
     * @param callable $callback
     * @return $this
     */
    public function registerCallback($callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    /**
     * Triggers the event callbacks.
     * @param string $file
     * @param string $event
     * @return bool
     */
    protected function triggerCallbacks($file, $event)
    {
        foreach ($this->callbacks as $callback) {
            if (call_user_func($callback, $file, $event, $this) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array|Context[] $contexts
     */
    public function setContexts(array $contexts)
    {
        $this->contexts = array();
        foreach ($contexts as $context) {
            $this->contexts[strtolower($context->getName())] = $context;
        }
        return $this;
    }

    /**
     * @param bool $enabled
     * @return Cleaner
     */
    public function setDryRun($enabled = true)
    {
        $this->dryRun = (bool)$enabled;
        return $this;
    }
}