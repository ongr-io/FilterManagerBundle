<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Event;

use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterInterface;
use Symfony\Component\EventDispatcher\Event;

class PreProcessSearchEvent extends Event
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var Search
     */
    private $relatedSearch;

    /**
     * Constructor
     *
     * @param FilterInterface $filter
     * @param Search $relatedSearch
     */
    public function __construct(FilterInterface $filter, Search $relatedSearch)
    {
        $this->filter = $filter;
        $this->relatedSearch = $relatedSearch;
    }

    /**
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return Search
     */
    public function getRelatedSearch()
    {
        return $this->relatedSearch;
    }
}
