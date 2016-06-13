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

use Doctrine\Common\Cache\Cache;
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
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $lifeTime;

    /**
     * @var array
     */
    private $exclude = [];

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
     * Sets cache engine
     *
     * @param Cache|null $cache
     */
    public function setCache(Cache $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Sets cached search life time
     *
     * @param $lifeTime
     */
    public function setLifeTime($lifeTime)
    {
        $this->lifeTime = $lifeTime;
    }

    /**
     * Sets array of filter names not to be cached
     *
     * @param array $exclude
     */
    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;
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
     * @param SearchRequest       $request
     * @param \ArrayIterator|null $filters
     *
     * @return Search
     */
    public function buildSearch(SearchRequest $request, $filters = null)
    {
        /** @var \ArrayIterator $filters */
        $filters = $filters ? $filters : $this->getIterator();
        $search = new Search();
        $cachedFilters = [];

        if ($this->cache) {
            foreach ($filters as $name => $filter) {
                if (!in_array($name, $this->exclude)) {
                    $cachedFilters[$name] = $request->get($name)->getSerializableData();
                }
            }

            $searchHash = md5(serialize($cachedFilters));

            if ($this->cache->contains($searchHash)) {
                $search = $this->cache->fetch($searchHash);
            } else {
                foreach ($cachedFilters as $name => $state) {
                    $filters->offsetGet($name)->modifySearch($search, $request->get($name), $request);
                }
                $this->cache->save($searchHash, $search, $this->lifeTime);
            }
        }

        foreach ($filters as $name => $filter) {
            if (!array_key_exists($name, $cachedFilters)) {
                $filter->modifySearch($search, $request->get($name), $request);
            }
        }

        return $search;
    }
}
