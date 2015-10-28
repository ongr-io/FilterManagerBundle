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

use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FuzzyQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class runs match search.
 */
class FuzzySearch extends AbstractSingleValue
{
    /**
     * @var array Fuzzy query parameters.
     */
    private $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            if (strpos($this->getField(), ',') !== false) {
                $subQuery = new BoolQuery();
                foreach (explode(',', $this->getField()) as $field) {
                    $subQuery->add(new FuzzyQuery($field, $state->getValue(), $this->getParameters()), 'should');
                }
                $search->addQuery($subQuery, 'must');
            } else {
                $search->addQuery(
                    new FuzzyQuery($this->getField(), $state->getValue(), $this->getParameters()),
                    'must'
                );
            }
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return array_filter($this->parameters);
    }

    /**
     * The maximum edit distance.
     *
     * @param string|int|float $fuzziness
     */
    public function setFuzziness($fuzziness)
    {
        $this->parameters['fuzziness'] = $fuzziness;
    }

    /**
     * The number of initial characters which will not be “fuzzified”.
     *
     * @param int $prefixLength
     */
    public function setPrefixLength($prefixLength)
    {
        $this->parameters['prefix_length'] = $prefixLength;
    }

    /**
     * The maximum number of terms that the fuzzy query will expand to.
     *
     * @param int $maxExpansions
     */
    public function setMaxExpansions($maxExpansions)
    {
        $this->parameters['max_expansions'] = $maxExpansions;
    }
}
