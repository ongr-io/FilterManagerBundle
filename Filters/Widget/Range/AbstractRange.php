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

use ONGR\ElasticsearchBundle\DSL\Filter\RangeFilter;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Helper\FieldAwareInterface;
use ONGR\FilterManagerBundle\Filters\Helper\FieldAwareTrait;
use ONGR\FilterManagerBundle\Filters\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filters\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Filters\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * Class AbstractRangeFilter.
 */
abstract class AbstractRange extends AbstractSingleRequestValueFilter implements
    FieldAwareInterface,
    ViewDataFactoryInterface
{
    use FieldAwareTrait;

    /**
     * @var bool
     */
    private $inclusive = false;

    /**
     * @param bool $inclusive
     *
     * @return $this
     */
    public function setInclusive($inclusive)
    {
        $this->inclusive = $inclusive;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInclusive()
    {
        return $this->inclusive;
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new RangeAwareViewData();
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
}
