<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter;

use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\Helper\DocumentFieldAwareInterface;
use ONGR\FilterManagerBundle\Filter\Helper\RequestFieldAwareInterface;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareInterface;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * This interface defines required methods for single filter.
 */
interface FilterInterface extends
    DocumentFieldAwareInterface,
    RequestFieldAwareInterface,
    RelationAwareInterface
{
    /**
     * This function is called right after filter is created. It passes options from configuration tree to the filter.
     * You are free to set any configuration you want in options node and it will be passed to your filter
     * by this function. This function is already defined in OptionsTrait used in AbstractFilter,
     * so do not forget to call parent if you extend it.
     *
     * @param array $options
     */
    public function setOptions(array $options);

    /**
     * Resolves filter state by given request.
     *
     * @param Request $request
     *
     * @return FilterState
     */
    public function getState(Request $request);

    /**
     * Modifies search request by given state. Usually should be used to add query or post_filter parameters.
     *
     * @param Search        $search  Search request.
     * @param FilterState   $state   Current filter state.
     * @param SearchRequest $request State of all filters.
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null);

    /**
     * Modifies search request by given state and related search. Usually is used to add aggregations into query.
     *
     * Related search does not include conditions from not related filters. Conditions made by filter
     * itself are also excluded on $relatedSearch. This method normally is called after modifySearch just before search
     * query execution
     *
     * @param Search      $search
     * @param Search      $relatedSearch
     * @param FilterState $state
     *
     * @return mixed
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null);

    /**
     * Prepares all needed filter data to pass into view.
     *
     * @param DocumentIterator $result Search results.
     * @param ViewData         $data   Initial view data.
     *
     * @return ViewData
     */
    public function getViewData(DocumentIterator $result, ViewData $data);

    /**
     * Returns all tags assigned to the filter.
     *
     * @return array
     */
    public function getTags();

    /**
     * Defines whether its necessary to build a related search for
     * the filters preProcessSearch() method
     *
     * @return bool
     */
    public function isRelated();
}
