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

use ONGR\ElasticsearchDSL\Filter\RangeFilter;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\FieldAwareInterface;
use ONGR\FilterManagerBundle\Filter\Helper\FieldAwareTrait;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\AbstractSingleRequestValueFilter;
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
