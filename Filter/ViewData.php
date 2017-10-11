<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter;

use ONGR\FilterManagerBundle\SerializableInterface;

/**
 * This class defines data structure passed into view by single filter.
 */
class ViewData implements SerializableInterface
{
    /**
     * @var FilterState
     */
    private $state;

    /**
     * @var array
     */
    private $tags = [];

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
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $tag
     *
     * @return bool
     */
    public function hasTag($tag)
    {
        return in_array($tag, $this->tags, true);
    }

    /**
     * @param array $tags
     */
    public function setTags($tags)
    {
        if (!is_array($tags)) {
            $this->tags = array_filter([ $tags ]);
        }

        $this->tags = $tags;
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
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        return [
            'name' => $this->name,
            'state' => $this->getState()->getSerializableData(),
            'tags' => $this->tags,
            'url_params' => $this->urlParameters,
            'reset_url_params' => $this->resetUrlParameters,
        ];
    }
}
