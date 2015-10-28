<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Search;

use ONGR\ElasticsearchDSL\Filter\BoolFilter;
use ONGR\ElasticsearchDSL\Filter\MissingFilter;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This filter selects only parent documents.
 */
class VariantFilter extends AbstractSingleValue
{
    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $filter = new MissingFilter($this->getField());
        $search->addFilter($filter, BoolFilter::MUST);
    }
}
