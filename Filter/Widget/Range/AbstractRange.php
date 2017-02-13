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

use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\AbstractFilter;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * Class AbstractRangeFilter.
 */
abstract class AbstractRange extends AbstractFilter implements ViewDataFactoryInterface
{
    /**
     * @return bool
     */
    public function isInclusive()
    {
        return $this->getOption('inclusive', false);
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
            $filter = new RangeQuery($this->getDocumentField(), $state->getValue());
            $search->addPostFilter($filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRelated()
    {
        return true;
    }
}
