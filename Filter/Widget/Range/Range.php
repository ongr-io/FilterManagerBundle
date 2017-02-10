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

        $stateValues[$this->isInclusive() ? 'gte' : 'gt'] = $values[0];
        $stateValues[$this->isInclusive() ? 'lte' : 'lt'] = $values[1];

        $state->setValue($this->normalizeStateValues($stateValues));

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        $stateAgg = new StatsAggregation($state->getName());
        $stateAgg->setField($this->getDocumentField());

        if ($relatedSearch->getPostFilters()) {
            $filterAgg = new FilterAggregation($state->getName() . '-filter', $relatedSearch->getPostFilters());
            $filterAgg->addAggregation($stateAgg);
            $stateAgg = $filterAgg;
        }

        $search->addAggregation($stateAgg);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        $name = $data->getState()->getName();

        if (!$agg = $result->getAggregation($name)) {
            $agg = $result->getAggregation($name . '-filter')->getAggregation($name);
        }

        /** @var $data ViewData\RangeAwareViewData */
        $data->setMinBounds($agg['min']);
        $data->setMaxBounds($agg['max']);

        return $data;
    }

    /**
     * @param array $stateValues
     * @return array
     */
    private function normalizeStateValues(array $stateValues)
    {
        if (!$this instanceof DateRange) {
            $stateValues = array_map('floatval', $stateValues);
        }

        return $stateValues;
    }
}
