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

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\FuzzyQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class runs match search.
 */
class FuzzySearch extends AbstractSingleValue
{
    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            if (strpos($this->getDocumentField(), ',') !== false) {
                $subQuery = new BoolQuery();
                foreach (explode(',', $this->getDocumentField()) as $field) {
                    $subQuery->add(new FuzzyQuery($field, $state->getValue(), $this->getOptions()), 'should');
                }
                $search->addQuery($subQuery, 'must');
            } else {
                $search->addQuery(
                    new FuzzyQuery($this->getDocumentField(), $state->getValue(), $this->getOptions()),
                    'must'
                );
            }
        }
    }
}
