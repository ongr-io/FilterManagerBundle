<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Choice;

use ONGR\ElasticsearchDSL\Aggregation\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\SizeAwareTrait;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Filter\Helper\FieldAwareInterface;
use ONGR\FilterManagerBundle\Filter\Helper\FieldAwareTrait;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class provides single terms choice.
 */
class SingleTermChoice extends AbstractSingleRequestValueFilter implements FieldAwareInterface, ViewDataFactoryInterface
{
    use FieldAwareTrait, SizeAwareTrait;

    /**
     * @var array
     */
    private $sortType;

    /**
     * @var bool
     */
    private $showZeroChoices;

    /**
     * @param array $sortType
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
    }

    /**
     * @return array
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * @param bool $showZeroChoices
     */
    public function setShowZeroChoices($showZeroChoices)
    {
        $this->showZeroChoices = $showZeroChoices;
    }

    /**
     * @return bool
     */
    public function getShowZeroChoices()
    {
        return $this->showZeroChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $search->addPostFilter(new TermQuery($this->getField(), $state->getValue()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        $name = $state ? $state->getName() : $this->getField();
        $aggregation = new TermsAggregation($name, $this->getField());

        if ($this->getSortType()) {
            $aggregation->addParameter('order', [$this->getSortType()['type'] => $this->getSortType()['order']]);
        }

        $aggregation->addParameter('size', 0);
        if ($this->getSize() > 0) {
            $aggregation->addParameter('size', $this->getSize());
        }

        if ($relatedSearch->getPostFilters()) {
            $filterAggregation = new FilterAggregation($name . '-filter');
            $filterAggregation->setFilter($relatedSearch->getPostFilters());
            $filterAggregation->addAggregation($aggregation);
            $search->addAggregation($filterAggregation);
        } else {
            $search->addAggregation($aggregation);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new ChoicesAwareViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        /** @var ChoicesAwareViewData $data */

        $unsortedChoices = [];

        foreach ($this->fetchAggregation($result, $data->getName()) as $bucket) {
            $active = $this->isChoiceActive($bucket['key'], $data);
            $choice = new ViewData\Choice();
            $choice->setLabel($bucket['key']);
            $choice->setCount($bucket['doc_count']);
            $choice->setActive($active);
            if ($active) {
                $choice->setUrlParameters($this->getUnsetUrlParameters($bucket['key'], $data));
            } else {
                $choice->setUrlParameters($this->getOptionUrlParameters($bucket['key'], $data));
            }
            $unsortedChoices[$bucket['key']] = $choice;
        }

        // Add the prioritized choices first.
        if ($this->getSortType()) {
            $unsortedChoices = $this->addPriorityChoices($unsortedChoices, $data);
        }

        foreach ($unsortedChoices as $choice) {
            $data->addChoice($choice);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isRelated()
    {
        return true;
    }

    /**
     * Adds prioritized choices.
     *
     * @param array                $unsortedChoices
     * @param ChoicesAwareViewData $data
     *
     * @return array
     */
    protected function addPriorityChoices(array $unsortedChoices, ChoicesAwareViewData $data)
    {
        foreach ($this->getSortType()['priorities'] as $name) {
            if (array_key_exists($name, $unsortedChoices)) {
                $data->addChoice($unsortedChoices[$name]);
                unset($unsortedChoices[$name]);
            }
        }

        return $unsortedChoices;
    }

    /**
     * Fetches buckets from search results.
     *
     * @param DocumentIterator $result Search results.
     * @param string           $name   Filter name.
     *
     * @return array Buckets.
     */
    protected function fetchAggregation(DocumentIterator $result, $name)
    {
        $aggregation = $result->getAggregation($name);
        if (isset($aggregation)) {
            return $aggregation;
        }

        $aggregation = $result->getAggregation(sprintf('%s-filter', $name));
        if (isset($aggregation)) {
            return $aggregation->find($name);
        }

        return [];
    }

    /**
     * @param string   $key
     * @param ViewData $data
     *
     * @return array
     */
    protected function getOptionUrlParameters($key, ViewData $data)
    {
        $parameters = $data->getResetUrlParameters();
        $parameters[$this->getRequestField()] = $key;

        return $parameters;
    }

    /**
     * Returns url with selected term disabled.
     *
     * @param string   $key
     * @param ViewData $data
     *
     * @return array
     */
    protected function getUnsetUrlParameters($key, ViewData $data)
    {
        return $data->getResetUrlParameters();
    }

    /**
     * Returns whether choice with the specified key is active.
     *
     * @param string   $key
     * @param ViewData $data
     *
     * @return bool
     */
    protected function isChoiceActive($key, ViewData $data)
    {
        return $data->getState()->isActive() && $data->getState()->getValue() == $key;
    }
}
