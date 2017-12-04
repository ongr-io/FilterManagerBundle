<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\Helper\OptionsAwareTrait;
use ONGR\FilterManagerBundle\Filter\ViewData;

/**
 * This class represents view data with page choices.
 */
class PagerAwareViewData extends ViewData
{
    use OptionsAwareTrait;

    /**
     * @var int Current page.
     */
    private $currentPage = 1;

    /**
     * @var int Total amount of items to show.
     */
    private $totalItems = 1;

    /**
     * @var int Maximum pages to show.
     */
    private $maxPages = 10;

    /**
     * @var int Maximum items show per page.
     */
    private $itemsPerPage = 12;

    /**
     * @var Number of pages to show.
     */
    private $numPages;

    /**
     * Initializes data for pagination.
     *
     * @param $totalItems
     * @param $currentPage
     * @param int         $itemsPerPage
     * @param int         $maxPages
     */
    public function setData($totalItems, $currentPage, $itemsPerPage = 12, $maxPages = 10)
    {
        $this->totalItems = $totalItems;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->numPages = (int) ceil($this->totalItems/$this->itemsPerPage);

        if ($maxPages < 3) {
            throw new \InvalidArgumentException('Max pages has to be not less than 3.');
        }

        $this->maxPages = $maxPages;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        $data = parent::getSerializableData();

        $data['pager'] = [
            'total_items' => $this->totalItems,
            'num_pages' => $this->numPages,
            'first_page' => 1,
            'previous_page' => $this->getPreviousPage(),
            'current_page' => $this->currentPage,
            'next_page' => $this->getNextPage(),
            'last_page' => $this->numPages,
        ];

        return $data;
    }

    /**
     * Get previous page number.
     *
     * @return int|null
     */
    public function getPreviousPage()
    {
        if ($this->currentPage > 1) {
            return $this->currentPage - 1;
        }

        return null;
    }

    /**
     * Returns current page number.
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Get next page number.
     *
     * @return int|null
     */
    public function getNextPage()
    {
        if ($this->currentPage < $this->numPages) {
            return $this->currentPage + 1;
        }

        return null;
    }

    /**
     * Returns the last page number.
     *
     * @return int
     */
    public function getLastPage()
    {
        return ceil($this->totalItems / $this->itemsPerPage);
    }

    /**
     * Returns true if the current page is first.
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->currentPage == 1;
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
        return $this->currentPage == $this->getLastPage();
    }

    /**
     * Generates a page list.
     *
     * @return array The page list.
     */
    public function getPages()
    {
        $this->maxPages--;

        $start = 1;
        $numAdjacents = (int) floor(($this->maxPages - 1) / 2);

        if ($this->currentPage - $numAdjacents < $start) {
            $begin = $start;
            $end = min($this->numPages, $this->maxPages);
        } elseif ($this->currentPage + $numAdjacents > $this->numPages) {
            $begin = max($start, $this->numPages - $this->maxPages + 1);
            $end = $this->numPages;
        } else {
            $begin = $this->currentPage - $numAdjacents + ($this->maxPages % 2);
            $end = $this->currentPage + $numAdjacents;
        }

        return array_unique(array_merge([1], range($begin, $end, 1), [$this->numPages]));
    }
}
