<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Search;

use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterInterface;
use ONGR\FilterManagerBundle\Relation\FilterIterator;
use ONGR\FilterManagerBundle\Relation\RelationInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class holds collection of FilterInterface objects labeled by name.
 */
class FilterContainer extends ParameterBag
{
    /**
     * {@inheritdoc}
     */
    public function add(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        if ($value instanceof FilterInterface) {
            parent::set($key, $value);
        }
    }

    /**
     * Filters accepted.
     *
     * @param RelationInterface $relation
     *
     * @return FilterInterface[]
     */
    public function getFiltersByRelation(RelationInterface $relation)
    {
        return new FilterIterator($this->getIterator(), $relation);
    }

    /**
     * Builds search request according to given filters.
     *
     * @param Request $request
     *
     * @return SearchRequest
     */
    public function buildSearchRequest(Request $request)
    {
        $search = new SearchRequest();
        /** @var FilterInterface[] $filters */
        $filters = $this->all();

        foreach ($filters as $name => $filter) {
            $state = $filter->getState($request);
            $state->setName($name);
            $search->set($name, $state);
        }

        return $search;
    }

    /**
     * Builds elastic search query by given SearchRequest and filters.
     *
     * @param SearchRequest          $request
     * @param FilterInterface[]|null $filters
     *
     * @return Search
     */
    public function buildSearch(SearchRequest $request, $filters = null)
    {
        $search = new Search();

        /** @var FilterInterface[] $filters */
        $filters = $filters ? $filters : $this->all();

        foreach ($filters as $name => $filter) {
            $filter->modifySearch($search, $request->get($name), $request);
        }

        return $search;
    }
}
