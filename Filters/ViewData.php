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

/**
 * This class defines data structure passed into view by single filter.
 */
class ViewData
{
    /**
     * @var FilterState
     */
    private $state;

    /**
     * @var array Url parameters representing current filter state.
     */
    private $urlParameters;

    /**
     * @var array Url parameters to reset filter.
     */
    private $resetUrlParameters;

    /**
     * @var string Filter name.
     */
    private $name;

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
     * @return array
     */
    public function getResetUrlParameters()
    {
        return $this->resetUrlParameters;
    }

    /**
     * @param array $resetUrlParameters
     */
    public function setResetUrlParameters($resetUrlParameters)
    {
        $this->resetUrlParameters = $resetUrlParameters;
    }

    /**
     * @return FilterState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param FilterState $state
     */
    public function setState(FilterState $state)
    {
        $this->state = $state;
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
}
