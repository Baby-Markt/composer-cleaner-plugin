<?php


namespace Babymarkt\Composer\Cleaner;


class Context
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $pattern = array();

    /**
     * @var array
     */
    protected $paths = array();

    /**
     * @var array
     */
    protected $exclude = array();

    /**
     * Context constructor.
     * @param array $values
     * @param string $name
     */
    public function __construct(array $values, $name = 'default')
    {
        $this->name = $name;

        foreach ($values as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{'set' . $key}($value);
            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @return array
     */
    public function getExclude()
    {
        return $this->exclude;
    }

    /**
     * @param array $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = (array) $pattern;
    }

    /**
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = (array) $paths;
    }

    /**
     * @param array $exclude
     */
    public function setExclude($exclude)
    {
        $this->exclude = (array) $exclude;
    }


}