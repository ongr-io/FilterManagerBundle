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

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
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
            if (strpos($this->getDocumentField(), ',') !== false) {
                $subQuery = new BoolQuery();
                foreach (explode(',', $this->getDocumentField()) as $field) {
                    $subQuery->add($this->buildMatchPart($field, $state), 'should');
                }
                $search->addQuery($subQuery, 'must');
            } else {
                $search->addQuery($this->buildMatchPart($this->getDocumentField(), $state), 'must');
            }
        }
    }

    /**
     * Build possibly nested match part.
     *
     * @param string      $field
     * @param FilterState $state
     *
     * @return BuilderInterface
     */
    private function buildMatchPart($field, FilterState $state)
    {
        if (strpos($field, '>') !== false) {
            list ($path, $field) = explode('>', $field);
        }

        if (strpos($field, '^') !== false) {
            list ($field, $boost) = explode('^', $field);
        }

        $query =  new MatchQuery($field, $state->getValue(), $this->getOptions());
        !isset($boost) ? : $query->addParameter('boost', $boost);

        if (isset($path)) {
            $query = new NestedQuery($path, $query);
        }

        return $query;
    }
}
