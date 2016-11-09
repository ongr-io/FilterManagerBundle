<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\ViewData;

use ONGR\FilterManagerBundle\SerializableInterface;

/**
 * This class holds data for filter choice.
 */
class ChoiceAwareViewData implements SerializableInterface
{
    /**
     * @var bool
     */
    private $active = false;

    /**
     * @var bool
     */
    private $default = false;

    /**
     * @var array Holds set or unset parameters depending on state.
     */
    private $urlParameters = [];

    /**
     * @var string
     */
    private $label;

    /**
     * @var string Sorting any arrays: "min", "max", for only numeric arrays: "avg", "sum".
     */
    private $mode = null;

    /**
     * @var int Represents document count for option.
     */
    private $count = 0;

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
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param bool $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
    public function setUrlParameters($urlParameters)
    {
        $this->urlParameters = $urlParameters;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        return [
            'active' => $this->isActive(),
            'default' => $this->isDefault(),
            'url_params' => $this->getUrlParameters(),
            'label' => $this->getLabel(),
            'mode' => $this->getMode(),
            'count' => $this->getCount(),
        ];
    }
}
