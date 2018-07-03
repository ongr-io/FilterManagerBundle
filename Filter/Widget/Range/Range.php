<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Range;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Metric\StatsAggregation;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;
use Symfony\Component\HttpFoundation\Request;

/**
 * Range filter, selects documents from lower limit to upper limit.
 */
class Range extends AbstractRange
{
    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = parent::getState($request);

        if (!$state->isActive()) {
            return $state;
        }

        $values = explode(';', $state->getValue(), 2);

        if (count($values) < 2) {
            $state->setActive(false);

            return $state;
        }

        $gt = $this->isInclusive() ? 'gte' : 'gt';
        $lt = $this->isInclusive() ? 'lte' : 'lt';

        $normalized[$gt] = $values[0];
        $normalized[$lt] = $values[1];

        $state->setValue($normalized);

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        $aggregation = new StatsAggregation($state->getName());
        $aggregation->setField($this->getDocumentField());

        if ($relatedSearch->getPostFilters()) {
            $filterAggregation = new FilterAggregation($state->getName() . '-filter');
            $filterAggregation->setFilter($relatedSearch->getPostFilters());
            $filterAggregation->addAggregation($aggregation);
            $aggregation = $filterAggregation;
        }

        $search->addAggregation($aggregation);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        $name = $data->getState()->getName();
        $aggregation = $result->getAggregation($name);

        if (!$aggregation) {
            $aggregation = $result->getAggregation($name . '-filter')->getAggregation($name);
        }

        /** @var $data ViewData\RangeAwareViewData */
        $data->setMinBounds($aggregation['min']);
        $data->setMaxBounds($aggregation['max']);

        return $data;
    }
}
