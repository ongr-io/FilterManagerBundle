<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Range;

use ONGR\ElasticsearchBundle\DSL\Aggregation\StatsAggregation;
use ONGR\ElasticsearchBundle\DSL\Filter\RangeFilter;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Helper\FieldAwareInterface;
use ONGR\FilterManagerBundle\Filters\Helper\FieldAwareTrait;
use ONGR\FilterManagerBundle\Filters\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Date range filter, selects documents from lower date to upper date.
 */
class DateRange extends AbstractSingleRequestValueFilter implements FieldAwareInterface, ViewDataFactoryInterface
{
    use FieldAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = parent::getState($request);

        if ($state->getValue()) {
            $values = explode(';', $state->getValue(), 2);
            $normalized['gt'] = $values[0];
            $normalized['lt'] = $values[1];

            $state->setValue($normalized);
        }

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        $stateAgg = new StatsAggregation('date_range_agg');
        $stateAgg->setField($this->getField());
        $search->addAggregation($stateAgg);
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $filter = new RangeFilter($this->getField(), $state->getValue());
            $search->addPostFilter($filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new ViewData\RangeAwareViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        /** @var $data ViewData\RangeAwareViewData */
        $data->setMinBounds(
           new \DateTime(
               date(
                   \DateTime::ISO8601,
                   $result->getAggregations()['date_range_agg']->getValue()['min'] / 1000
               )
           )
        );

        $data->setMaxBounds(
            new \DateTime(
                date(
                    \DateTime::ISO8601,
                    $result->getAggregations()['date_range_agg']->getValue()['max'] / 1000
                )
            )
        );

        return $data;
    }
}
