<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Pager;

use ONGR\FilterManagerBundle\Pager\PagerService;

class PagerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns mock object for PagerAdapterInterface.
     *
     * @param int   $totalResults Return value of getTotalResults method.
     * @param array $results      Return value of getResults method.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getPagerAdapterMock($totalResults = 1000, $results = [])
    {
        $mock = $this->getMockBuilder('ONGR\FilterManagerBundle\Pager\PagerAdapterInterface')
            ->getMock();

        $mock->expects($this->any())
            ->method('getTotalResults')
            ->will($this->returnValue($totalResults));

        $mock->expects($this->any())
            ->method('getResults')
            ->will($this->returnValue($results));

        return $mock;
    }

    /**
     * Data provider for testGetNextPage().
     *
     * @return array
     */
    public function getTestGetNextPageData()
    {
        return [
            // Case #1.
            [500, 10, 5, 6],
            // Case #2.
            [500, 10, 0, 2],
            // Case #3.
            [500, 10, 50, 50],
            // Case #4.
            [0, 20, 1, 1],
        ];
    }

    /**
     * Tests getNextPage method.
     *
     * @param int $totalResults Total count of results.
     * @param int $limit        Max count of results per page.
     * @param int $page         Current page.
     * @param int $expected     Expected result.
     *
     * @dataProvider getTestGetNextPageData
     */
    public function testGetNextPage($totalResults, $limit, $page, $expected)
    {
        $mockPagerAdapter = $this->getPagerAdapterMock($totalResults);

        $pager = new PagerService($mockPagerAdapter, [ 'limit' => $limit, 'page' => $page ]);
        $result = $pager->getNextPage();
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for testGetPreviousPage().
     *
     * @return array
     */
    public function getTestGetPreviousPageData()
    {
        return [
            // Case #1.
            [500, 10, 6, 5],
            // Case #2.
            [500, 10, 60, 49],
            // Case #3.
            [500, 10, 1, 1],
            // Case #4.
            [0, 20, 3, 1],
        ];
    }

    /**
     * Tests getPreviousPage method.
     *
     * @param int $totalResults Total count of results.
     * @param int $limit        Max count of results per page.
     * @param int $page         Current page.
     * @param int $expected     Expected result.
     *
     * @dataProvider getTestGetPreviousPageData
     */
    public function testGetPreviousPage($totalResults, $limit, $page, $expected)
    {
        $mockPagerAdapter = $this->getPagerAdapterMock($totalResults);

        $pager = new PagerService($mockPagerAdapter, [ 'limit' => $limit, 'page' => $page ]);
        $result = $pager->getPreviousPage();
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests isFirstPage method.
     */
    public function testIsFirstPage()
    {
        $mockPagerAdapter = $this->getPagerAdapterMock(500);

        $pager = new PagerService($mockPagerAdapter, [ 'limit' => 10, 'page' => 1 ]);
        $this->assertTrue($pager->isFirstPage());

        $pager->setPage(2);
        $this->assertFalse($pager->isFirstPage());
    }

    /**
     * Tests isLastPage method.
     */
    public function testIsLastPage()
    {
        $mockPagerAdapter = $this->getPagerAdapterMock(500);

        $pager = new PagerService($mockPagerAdapter, [ 'limit' => 10, 'page' => 5 ]);
        $this->assertFalse($pager->isLastPage());

        $pager->setPage(50);
        $this->assertTrue($pager->isLastPage());
    }

    /**
     * Data provider for testIsPaginable().
     *
     * @return array
     */
    public function getTestIsPaginableData()
    {
        return [
            // Case #1.
            [0, 5, false],
            // Case #2.
            [10, 10, false],
            // Case #3.
            [21, 20, true],
        ];
    }

    /**
     * Tests isPaginable method.
     *
     * @param int  $totalResults Total count of results.
     * @param int  $limit        Max count of results per page.
     * @param bool $expected     Expected result.
     *
     * @dataProvider getTestIsPaginableData
     */
    public function testIsPaginable($totalResults, $limit, $expected)
    {
        $mockPagerAdapter = $this->getPagerAdapterMock($totalResults);

        $pager = new PagerService($mockPagerAdapter, [ 'limit' => $limit, 'page' => 1 ]);
        $this->assertEquals($expected, $pager->isPaginable());
    }

    /**
     * Data provider for testGetPages().
     *
     * @return array
     */
    public function getTestGetPagesData()
    {
        return [
            // Case #1 (odd pages count, shown page in the middle).
            [500, 10, 20, 5, [18, 19, 20, 21, 22]],
            // Case #2 (odd pages count, shown page at the beginning).
            [500, 10, 2, 5, [1, 2, 3, 4, 5]],
            // Case #3 (more max pages count than actual count, first page shown).
            [50, 10, 1, 8, [1, 2, 3, 4, 5]],
            // Case #4 (no results).
            [0, 10, 1, 5, [1]],
            // Case #5 (even pages count, shown page in the middle).
            [500, 10, 47, 4, [45, 46, 47, 48]],
            // Case #6 (odd pages count, last page shown).
            [500, 10, 50, 3, [48, 49, 50]],
            // Case #7 (max one page, last page shown).
            [500, 10, 50, 1, [50]],
            // Case #8 (max pages count equals actual count, last page shown).
            [21, 10, 3, 3, [1, 2, 3]],
            // Case #9 (max pages count equals actual count, last page shown).
            [31, 10, 4, 4, [1, 2, 3, 4]],
            // Case #10 (max pages count equals actual count, first page shown).
            [31, 10, 1, 4, [1, 2, 3, 4]],
            // Case #11 (max pages count equals actual count, middle page shown).
            [31, 10, 3, 4, [1, 2, 3, 4]],
        ];
    }

    /**
     * Tests getPages method.
     *
     * @param int   $totalResults Total count of results.
     * @param int   $limit        Max count of results per page.
     * @param int   $page         Current page.
     * @param int   $maxPages     Max count of pages shown.
     * @param array $expected     Expected result.
     *
     * @dataProvider getTestGetPagesData
     */
    public function testGetPages($totalResults, $limit, $page, $maxPages, $expected)
    {
        $mockPagerAdapter = $this->getPagerAdapterMock($totalResults);

        $pager = new PagerService(
            $mockPagerAdapter,
            [ 'limit' => $limit, 'page' => $page, 'max_pages' => $maxPages ]
        );
        $result = $pager->getPages();
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for testGetResults().
     *
     * @return array
     */
    public function getTestGetResultsData()
    {
        return [
            // Case #1 (one item result).
            [1, [5], [5]],
            // Case #2 (if adapter has totalResult = 0, return empty array).
            [0, [1, 2, 3], []],
            // Case #3 (more items in result).
            [5, [1, 2, 3, 4, 5], [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * Tests getResults method.
     *
     * @param int   $totalResults Total count of results.
     * @param array $results      Results returned from mock PagerAdapter.
     * @param array $expected     Expected result.
     *
     * @dataProvider getTestGetResultsData
     */
    public function testGetResults($totalResults, $results, $expected)
    {
        $mockPagerAdapter = $this->getPagerAdapterMock($totalResults, $results);

        $pager = new PagerService($mockPagerAdapter, [ 'page' => 1 ]);
        $result = $pager->getResults();
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for testGetOffset().
     *
     * @return array
     */
    public function getTestGetOffsetData()
    {
        return [
            // Case #1.
            [1, 10, 0],
            // Case #2.
            [5, 12, 48],
            // Case #3.
            [6, 1, 5],
        ];
    }

    /**
     * Tests getOffset method.
     *
     * @param int $page     Current page.
     * @param int $limit    Max count of results per page.
     * @param int $expected Expected result.
     *
     * @dataProvider getTestGetOffsetData
     */
    public function testGetOffset($page, $limit, $expected)
    {
        $mockPagerAdapter = $this->getPagerAdapterMock();

        $pager = new PagerService($mockPagerAdapter, [ 'page' => $page, 'limit' => $limit ]);
        $result = $pager->getOffset();
        $this->assertEquals($expected, $result);
    }
}
