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
     * Cleaner constructor.
     * @param Filesystem $filesystem
     * @param array|Context[] $contexts
     */
    public function __construct(Filesystem $filesystem, array $contexts)
    {
        $this->filesystem = $filesystem;

        $this->contexts = array();
        foreach ($contexts as $context) {
            $this->contexts[strtolower($context->getName())] = $context;
        }
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

        $paths = $this->getNormalizedPaths($contextName);

        foreach ($this->contexts[$contextName]->getPattern() as $globPattern) {
            foreach ($paths as $path) {
                $this->remove($path . DIRECTORY_SEPARATOR . $globPattern);
            }
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
            if (!$this->isExcluded($path)) {
                if ($this->triggerCallbacks($path, self::EVENT_PRE_REMOVE)) {
                    if ($this->filesystem->remove($path)) {
                        $this->triggerCallbacks($path, self::EVENT_REMOVE_SUCCESSFUL);
                    } else {
                        $this->triggerCallbacks($path, self::EVENT_REMOVE_FAILED);
                    }
                }
            }
        }
    }

    /**
     * @param $path
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
     * @param $callback
     * @return $this
     */
    public function registerCallback($callback)
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    /**
     * Triggers the event callbacks.
     * @param $file
     * @param $event
     * @return bool
     */
    protected function triggerCallbacks($file, $event)
    {
        foreach ($this->callbacks as $callback) {
            if (!call_user_func($callback, $file, $event, $this)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if a given context name exists.
     * @param $contextName
     * @return bool
     */
    protected function contextExists($contextName)
    {
        foreach ($this->contexts as $context) {
            if ($context->getName() === $contextName) {
                return true;
            }
        }
        return false;
    }
}