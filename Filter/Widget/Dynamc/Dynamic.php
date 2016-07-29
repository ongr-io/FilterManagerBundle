<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Dynamic;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterInterface;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareTrait;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Relation\RelationInterface;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class provides sorting filter.
 */
class Dynamic implements FilterInterface
{
    use RelationAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        // TODO: Implement getState() method.
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        // TODO: Implement modifySearch() method.
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        // TODO: Implement preProcessSearch() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        // TODO: Implement getViewData() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        // TODO: Implement getTags() method.
    }
}
