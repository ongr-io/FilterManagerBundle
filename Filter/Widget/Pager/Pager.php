<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Pager;

use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\ViewData\PagerAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\AbstractFilter;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use ONGR\FilterManagerBundle\Pager\PagerService;
use ONGR\FilterManagerBundle\Pager\Adapters\CountAdapter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides basic functionality for pagination.
 */
class Pager extends AbstractFilter implements ViewDataFactoryInterface
{
    /**
     * @return int
     */
    public function getMaxPages()
    {
        return $this->getOption('max_pages', 10);
    }

    /**
     * Returns count per page.
     *
     * @return int
     */
    public function getCountPerPage()
    {
        return $this->getOption('count_per_page', 12);
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
            $search->setFrom($this->getCountPerPage() * ($state->getValue() - 1));
        }

        $search->setSize($this->getCountPerPage());
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
        return new PagerAwareViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        /** @var ViewData\PagerAwareViewData $data */
        $data->setPager(
            new PagerService(
                new CountAdapter($result->count()),
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

    /**
     * {@inheritdoc}
     */
    public function isRelated()
    {
        return false;
    }
}
