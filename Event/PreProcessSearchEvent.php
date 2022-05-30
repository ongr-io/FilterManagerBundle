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
use ONGR\FilterManagerBundle\Filter\FilterState;
use Symfony\Contracts\EventDispatcher\Event;

class PreProcessSearchEvent extends Event
{
    /**
     * @var FilterState
     */
    private $state;

    /**
     * @var Search
     */
    private $relatedSearch;

    /**
     * Constructor
     *
     * @param FilterState $state
     * @param Search $relatedSearch
     */
    public function __construct(FilterState $state, Search $relatedSearch)
    {
        $this->state = $state;
        $this->relatedSearch = $relatedSearch;
    }

    /**
     * @return FilterState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return Search
     */
    public function getRelatedSearch()
    {
        return $this->relatedSearch;
    }
}
