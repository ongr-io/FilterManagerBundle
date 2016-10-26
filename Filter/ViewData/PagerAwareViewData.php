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
     * @param int $itemsPerPage
     * @param int $maxPages
     */
    public function setData($totalItems, $currentPage, $itemsPerPage = 12, $maxPages = 10)
    {
        $this->totalItems = $totalItems;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;

//        if ($maxPages < 3) {
//            throw new \InvalidArgumentException('Max pages has to be more than 3.');
//        }
        $this->maxPages = $maxPages;

        $this->numPages = (int) ceil($this->totalItems/$this->itemsPerPage);
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
        $numAdjacents = (int) floor(($this->maxPages - 3) / 2);

        if ($this->currentPage + $numAdjacents > $this->numPages) {
            $begin = $this->numPages - $this->maxPages + 2;
        } else {
            $begin = $this->currentPage - $numAdjacents;
        }
        if ($begin < 2) $begin = 2;

        $end = $begin + $this->maxPages - 2;
//        if ($end >= $this->numPages) $end = $this->numPages - 1;

//        $tmpBegin = $this->maxPages - floor($this->maxPages / 2);
//        $tmpEnd = $tmpBegin + $this->maxPages - 1;
//
//        if ($tmpBegin < 1) {
//            $tmpEnd += 1 - $tmpBegin;
//            $tmpBegin = 1;
//        }
//
//        if ($tmpEnd > $this->getLastPage()) {
//            $tmpBegin -= $tmpEnd - $this->getLastPage();
//            $tmpEnd = $this->getLastPage();
//        }
//
//        $begin = min($tmpBegin, 2);
//        $end = $tmpEnd;

        return range($begin, $end, 1);
    }
}
