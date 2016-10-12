<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Helper;

/**
 * A trait which handles the behavior of options in filters.
 */
trait OptionsAwareTrait
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * Checks if parameter exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Removes parameter.
     *
     * @param string $name
     */
    public function removeOption($name)
    {
        if ($this->hasOption($name)) {
            unset($this->options[$name]);
        }
    }

    /**
     * Returns one parameter by it's name.
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }
        return $default;
    }

    /**
     * Returns an array of all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string                 $name
     * @param array|string|\stdClass $value
     */
    public function addOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Sets an array of options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}
