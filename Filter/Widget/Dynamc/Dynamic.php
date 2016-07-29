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
     * @var string
     */
    private $requestField;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $value = $request->get($this->getRequestField());

        if (isset($value) && $value !== '') {

        }
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

    /**
     * @return mixed
     */
    public function getRequestField()
    {
        return $this->requestField;
    }

    /**
     * @param mixed $requestField
     */
    public function setRequestField($requestField)
    {
        $this->requestField = $requestField;
    }

    /**
     * @return FilterInterface
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param FilterInterface $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }
}
