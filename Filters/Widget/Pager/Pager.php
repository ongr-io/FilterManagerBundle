<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Pager;

use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filters\FilterInterface;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use ONGR\FilterManagerBundle\Pager\PagerService;
use ONGR\FilterManagerBundle\Pager\Adapters\CountAdapter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides basic functionality for pagination.
 */
class Pager extends AbstractSingleRequestValueFilter implements FilterInterface, ViewDataFactoryInterface
{
    /**
     * @var int
     */
    private $countPerPage;

    /**
     * @var int
     */
    private $maxPages;

    /**
     * @return int
     */
    public function getMaxPages()
    {
        return $this->maxPages;
    }

    /**
     * @param int $maxPages
     */
    public function setMaxPages($maxPages)
    {
        $this->maxPages = $maxPages;
    }

    /**
     * Sets count per page.
     *
     * @param int $count
     */
    public function setCountPerPage($count)
    {
        $this->countPerPage = $count;
    }

    /**
     * Returns count per page.
     *
     * @return int
     */
    public function getCountPerPage()
    {
        return $this->countPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = parent::getState($request);
        // Reset pager with any filter.
        $state->setUrlParameters([]);
        $page = (integer)$state->getValue();
        $state->setValue($page < 1 ? 1 : $page);

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $search->setFrom($this->countPerPage * ($state->getValue() - 1));
        }

        $search->setSize($this->countPerPage);
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        // Nothing to do here.
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new ViewData\PagerAwareViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        /** @var ViewData\PagerAwareViewData $data */
        $data->setPager(
            new PagerService(
                new CountAdapter($result->getTotalCount()),
                array_filter(
                    [
                        'page' => $data->getState()->getValue(),
                        'limit' => $this->getCountPerPage(),
                        'max_pages' => $this->getMaxPages(),
                    ]
                )
            )
        );

        return $data;
    }
}
