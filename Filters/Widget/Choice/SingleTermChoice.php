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

        /** @var ValueAggregation $bucket */
        foreach ($this->fetchAggregation($result, $data->getName()) as $bucket) {
            $bucket = $bucket->getValue();
            $active = $data->getState()->isActive() && $data->getState()->getValue() == $bucket['key'];
            $choice = new ViewData\Choice();
            $choice->setLabel($bucket['key']);
            $choice->setCount($bucket['doc_count']);
            $choice->setActive($active);
            if ($active) {
                $choice->setUrlParameters($data->getResetUrlParameters());
            } else {
                $choice->setUrlParameters($this->getOptionUrlParameters($bucket['key'], $data));
            }
            $data->addChoice($choice);
        }

        return $data;
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
}
