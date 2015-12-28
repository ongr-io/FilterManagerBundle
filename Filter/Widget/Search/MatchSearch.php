<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Search;

use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class runs match search.
 */
class MatchSearch extends AbstractSingleValue
{
    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            if (strpos($this->getField(), ',') !== false) {
                $subQuery = new BoolQuery();
                foreach (explode(',', $this->getField()) as $field) {
                    if (strpos($field, '^') === false) {
                        $subQuery->add(new MatchQuery($field, $state->getValue()), 'should');
                    } else {
                        list ($field, $boost) = explode('^', $field);

                        $subQuery->add(new MatchQuery($field, $state->getValue(), ['boost' => $boost]), 'should');
                    }
                }
                $search->addQuery($subQuery, 'must');
            } else {
                $search->addQuery(new MatchQuery($this->getField(), $state->getValue()), 'must');
            }
        }
    }
}
