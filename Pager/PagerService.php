<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Pager;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Returns all the required data to paginate.
 */
class PagerService
{
    /**
     * @var int Current page.
     */
    private $page;

    /**
     * @var int Number of items per page.
     */
    private $limit;

    /**
     * @var int Maximum number of pages.
     */
    private $maxPages;

    /**
     * Constructor.
     *
     * @param PagerAdapterInterface $adapter The pager adapter.
     * @param array                 $options Additional options.
     */
    public function __construct(PagerAdapterInterface $adapter, array $options = [])
    {
        $this->adapter = $adapter;
        $resolver = new OptionsResolver();
        $this->setRequiredOptions($resolver);
        $options = $resolver->resolve($options);
        $this->setLimit($options['limit']);
        $this->setPage($options['page']);
        $this->setMaxPages($options['max_pages']);
    }

    /**
     * Sets the required options.
     *
     * @param OptionsResolver $resolver
     */
    private function setRequiredOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['limit', 'page', 'max_pages'])
            ->setDefaults(['max_pages' => 8, 'limit' => 10, 'page' => 1]);
    }

    /**
     * Sets the current page number.
     *
     * @param int $page The current page number.
     */
    public function setPage($page)
    {
        $this->page = min($page > 0 ? $page : $this->getFirstPage(), $this->getLastPage());
    }

    /**
     * Returns the current page number.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Sets the results limit for one page.
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit > 0 ? $limit : 1;

        $this->setPage($this->page);
    }

    /**
     * Returns the current results limit for one page.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the number of pages shown.
     *
     * @param int $maxPages
     */
    public function setMaxPages($maxPages)
    {
        $this->maxPages = $maxPages;
    }

    /**
     * Returns the number of pages shown.
     *
     * @return int
     */
    public function getMaxPages()
    {
        return $this->maxPages;
    }

    /**
     * Returns the next page number.
     *
     * @return int
     */
    public function getNextPage()
    {
        $lastPage = $this->getLastPage();

        return $this->page < $lastPage ? $this->page + 1 : $lastPage;
    }

    /**
     * Returns the previous page number.
     *
     * @return int
     */
    public function getPreviousPage()
    {
        return $this->page > $this->getFirstPage() ? $this->page - 1 : $this->getFirstPage();
    }

    /**
     * Returns true if the current page is first.
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->page == 1;
    }

    /**
     * Returns the first page number.
     *
     * @return int
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * Returns true if the current page is last.
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->page == $this->getLastPage();
    }

    /**
     * Returns the last page number.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->hasResults() ? ceil($this->adapter->getTotalResults() / $this->limit) : $this->getFirstPage();
    }

    /**
     * Returns true if the current result set requires pagination.
     *
     * @return bool
     */
    public function isPaginable()
    {
        return $this->adapter->getTotalResults() > $this->limit;
    }

    /**
     * Generates a page list.
     *
     * @return array The page list.
     */
    public function getPages()
    {
        $pages = $this->getMaxPages();

        $tmpBegin = $this->page - floor($pages / 2);
        $tmpEnd = $tmpBegin + $pages - 1;

        if ($tmpBegin < $this->getFirstPage()) {
            $tmpEnd += $this->getFirstPage() - $tmpBegin;
            $tmpBegin = $this->getFirstPage();
        }

        if ($tmpEnd > $this->getLastPage()) {
            $tmpBegin -= $tmpEnd - $this->getLastPage();
            $tmpEnd = $this->getLastPage();
        }

        $begin = max($tmpBegin, $this->getFirstPage());
        $end = $tmpEnd;

        return range($begin, $end, 1);
    }

    /**
     * Returns true if the current result set is not empty.
     *
     * @return bool
     */
    public function hasResults()
    {
        return $this->adapter->getTotalResults() > 0;
    }

    /**
     * Returns results list for the current page and limit.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->hasResults() ? $this->adapter->getResults($this->getOffset(), $this->limit) : [];
    }

    /**
     * Returns offset.
     *
     * @return int
     */
    public function getOffset()
    {
        return ($this->page - 1) * $this->limit;
    }

    /**
     * Returns the current adapter instance.
     *
     * @return PagerAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
