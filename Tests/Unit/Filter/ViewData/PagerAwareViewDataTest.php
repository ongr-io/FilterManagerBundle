<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\ViewData\PagerAwareViewData;

class PagerAwareViewDataTest extends \PHPUnit_Framework_TestCase
{
    public function testSetData()
    {
        $pagerData = new PagerAwareViewData();
        $pagerData->setState($this->createMock('ONGR\FilterManagerBundle\Filter\FilterState'));

        $this->assertEquals(
            [
                'total_items' => 1,
                'num_pages' => null,
                'first_page' => 1,
                'previous_page' => null,
                'current_page' => 1,
                'next_page' => null,
                'last_page' => null,

            ],
            $pagerData->getSerializableData()['pager']
        );

        $pagerData->setData(100, 2, 12, 5);

        $this->assertEquals(
            [
                'total_items' => 100,
                'num_pages' => 9,
                'first_page' => 1,
                'previous_page' => 1,
                'current_page' => 2,
                'next_page' => 3,
                'last_page' => 9,
            ],
            $pagerData->getSerializableData()['pager']
        );

        $this->assertEquals(2, $pagerData->getCurrentPage());
    }

    public function testCheckPageNavigation()
    {
        $pagerData = new PagerAwareViewData();
        $pagerData->setState($this->createMock('ONGR\FilterManagerBundle\Filter\FilterState'));
        $pagerData->setData(100, 1, 12, 5);

        $this->assertTrue($pagerData->isFirstPage());
        $this->assertFalse($pagerData->isLastPage());
        $this->assertEquals(2, $pagerData->getNextPage());

        $pagerData->setData(100, 9, 12, 5);
        $this->assertTrue($pagerData->isLastPage());
        $this->assertFalse($pagerData->isFirstPage());
        $this->assertEquals(1, $pagerData->getFirstPage());
    }

    /**
     * @dataProvider getPagesDataProvider
     * @param int $totalItems
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param int $maxPages
     * @param array $resultRange
     */
    public function testGetPages($totalItems, $itemsPerPage, $currentPage, $maxPages, $resultRange)
    {
        $pagerData = new PagerAwareViewData();
        $pagerData->setState($this->createMock('ONGR\FilterManagerBundle\Filter\FilterState'));

        $pagerData->setData($totalItems, $currentPage, $itemsPerPage, $maxPages);

        $this->assertEquals(array_values($resultRange), array_values($pagerData->getPages()));
    }

    public function getPagesDataProvider()
    {
        return [
            // [1]
            [
                'totalItems'    => 1,
                'itemsPerPage'  => 10,
                'currentPage'   => 1,
                'maxPages'      => 5,
                'resultRange' => [1]
            ],
            // 1 [2]
            [
                'totalItems'    => 20,
                'itemsPerPage'  => 10,
                'currentPage'   => 2,
                'maxPages'      => 5,
                'resultRange' => [1, 2]
            ],
            // [1] 2 3 4 ... 10
            [
                'totalItems' => 100,
                'itemsPerPage' => 10,
                'currentPage' => 1,
                'maxPages' => 5,
                'resultRange' => [1, 2, 3, 4, 10]
            ],
            // 1 ... 4 [5] 6 ... 10
            [
                'totalItems' => 100,
                'itemsPerPage' => 10,
                'currentPage' => 5,
                'maxPages' => 5,
                'resultRange' => [1, 4, 5, 6, 10],
            ],
            // 1 ... 4 [5] 6 7 ... 10
            [
                'totalItems' => 100,
                'itemsPerPage' => 10,
                'currentPage' => 5,
                'maxPages' => 6,
                'resultRange' => [1, 4, 5, 6, 7, 10],
            ],
            // 1 ...  6 7 8 [9]
            [
                'totalItems' => 100,
                'itemsPerPage' => 12,
                'currentPage' => 9,
                'maxPages' => 5,
                'resultRange' => array_merge([1], range(6, 8, 1), [9])
            ],
            // 1 ... 37 38 39 [40]
            [
                'totalItems' => 200,
                'itemsPerPage' => 5,
                'currentPage' => 40,
                'maxPages' => 5,
                'resultRange' => array_merge([1], range(37, 39, 1), [40])
            ],
            // 1 ... 18 19 20 21 22 23 ... [40]
            [
                'totalItems' => 200,
                'itemsPerPage' => 5,
                'currentPage' => 20,
                'maxPages' => 8,
                'resultRange' => array_merge([1], range(18, 23, 1), [40])
            ],
            // 1 ... 7 8 9 [10] 11 12 13 14 ... 100
            [
                'totalItems' => 1000,
                'itemsPerPage' => 10,
                'currentPage' => 10,
                'maxPages' => 10,
                'resultRange' => array_merge([1], range(7, 14, 1), [100])
            ],
        ];
    }
}
