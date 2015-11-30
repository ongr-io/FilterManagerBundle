<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters;

use ONGR\FilterManagerBundle\SerializableInterface;

/**
 * This class defines data structure to represent filter state.
 */
class FilterState implements SerializableInterface
{
    /**
     * @var bool True if filter is currently.
     */
    private $active = false;

    /**
     * @var mixed Represents user selected value for filtering.
     */
    private $value;

    /**
     * @var array Url parameters related *only* to given filter.
     */
    private $urlParameters = [];

    /**
     * @var string Filter name.
     */
    private $name;

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getUrlParameters()
    {
        return $this->urlParameters;
    }

    /**
     * @param array $urlParameters
     */
    public function setUrlParameters(array $urlParameters)
    {
        $this->urlParameters = $urlParameters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        return [
            'active' => $this->active,
            'value' => $this->value,
        ];
    }
}
