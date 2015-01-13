<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Choice;

use ONGR\ElasticsearchBundle\DSL\Aggregation\FilterAggregation;
use ONGR\ElasticsearchBundle\DSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchBundle\DSL\Filter\TermFilter;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\Result\Aggregation\ValueAggregation;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filters\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Filters\Helper\FieldAwareInterface;
use ONGR\FilterManagerBundle\Filters\Helper\FieldAwareTrait;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class provides single terms choice.
 */
class SingleTermChoice extends AbstractSingleRequestValueFilter implements FieldAwareInterface, ViewDataFactoryInterface
{
    use FieldAwareTrait;

    /**
     * @var array
     */
    private $sortType;

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
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $search->addPostFilter(new TermFilter($this->getField(), $state->getValue()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        $name = $state ? $state->getName() : $this->getField();
        $agg = new TermsAggregation($name);
        $agg->setField($this->getField());

        if ($this->getSortType()) {
            $agg->setOrder($this->sortType['type'], $this->sortType['order']);
        }

        if ($relatedSearch->getPostFilters() && $relatedSearch->getPostFilters()->isRelevant()) {
            $filterAgg = new FilterAggregation($name . '-filter');
            $filterAgg->setFilter($relatedSearch->getPostFilters());
            $filterAgg->aggregations->addAggregation($agg);
            $search->addAggregation($filterAgg);
        } else {
            $search->addAggregation($agg);
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

        /** @var ValueAggregation $bucket */
        foreach ($this->fetchAggregation($result, $data->getName()) as $bucket) {
            $bucket = $bucket->getValue();
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
     * @return array Buckets
     */
    protected function fetchAggregation(DocumentIterator $result, $name)
    {
        $aggregations = $result->getAggregations();
        if (isset($aggregations[$name])) {
            return $aggregations[$name];
        }

        $buckets = $aggregations->find(sprintf('%s-filter.%s', $name, $name));

        if (isset($buckets)) {
            return $buckets;
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
