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

use ONGR\FilterManagerBundle\Pager\Adapters\CountAdapter;
use ONGR\FilterManagerBundle\Pager\PagerService;
use Elasticsearch\Endpoints\Count;

/**
 * Unit tests pager service.
 */
class PagerServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns dummy data for testing.
     *
     * @return array
     */
    protected function getDummyData()
    {
        return [
            'item1',
            'item2',
            'item3',
            'item4',
            'item5',
        ];
    }

    /**
     * Data provider for testConstruct().
     *
     * @return array
     */
    public function getConstructData()
    {
        $out = [];
        // Case #0 default values.
        $out[] = [['page' => 1], 10, 1, 8];
        // Case #1 custom values.
        $options = [
            'limit' => 3,
            'page' => 4,
            'max_pages' => 100,
        ];
        $out[] = [$options, 3, 2, 100];

        return $out;
    }

    /**
     * Test for constructor.
     *
     * @param array $options
     * @param int   $expectedLimit
     * @param int   $expectedPage
     * @param int   $expectedMaxPages
     *
     * @dataProvider getConstructData()
     */
    public function testConstruct($options, $expectedLimit, $expectedPage, $expectedMaxPages)
    {
        $pager = new PagerService(new CountAdapter($this->getDummyData()), $options);
        $this->assertEquals($expectedLimit, $pager->getLimit());
        $this->assertEquals($expectedMaxPages, $pager->getMaxPages());
        $this->assertEquals($expectedPage, $pager->getPage());
    }

    /**
     * Data provider for testGetOffset().
     *
     * @return array
     */
    public function getOffsetData()
    {
        $out = [];
        // Case #0 one item per page, should be five pages.
        $options = [
            'limit' => 1,
            'page' => 1,
            'max_pages' => 100,
        ];

        $expectedData = [
            0,
            1,
            2,
            3,
            4,
        ];
        $out[] = [$options, $expectedData];
        // Case #1 two items per page, should be three pages.
        $options = [
            'limit' => 2,
            'page' => 0,
            'max_pages' => 100,
        ];
        $expectedData = [
            0,
            2,
            4,
        ];
        $out[] = [$options, $expectedData];

        return $out;
    }

    /**
     * Check if ranges returned is correct.
     *
     * @param array $options
     * @param array $expectedData
     *
     * @dataProvider getOffsetData()
     */
    public function testGetOffset($options, $expectedData)
    {
        $pager = new PagerService(new CountAdapter($this->getDummyData()), $options);
        foreach ($expectedData as $expectedBounds) {
            $this->assertEquals($expectedBounds, $pager->getOffset());
            $pager->setPage($pager->getNextPage());
        }
        $expectedData = array_reverse($expectedData);
        foreach ($expectedData as $expectedBounds) {
            $this->assertEquals($expectedBounds, $pager->getOffset());
            $pager->setPage($pager->getPreviousPage());
        }
        $this->assertEquals(count($expectedData), $pager->getLastPage());
    }

    /**
     * Check if first, last page check returns expected value.
     */
    public function testIsFirstLastPage()
    {
        $pager = new PagerService(new CountAdapter($this->getDummyData()), ['limit' => 3]);
        $this->assertTrue($pager->isFirstPage());
        $this->assertFalse($pager->isLastPage());
        $pager->setPage($pager->getNextPage());
        $this->assertFalse($pager->isFirstPage());
        $this->assertTrue($pager->isLastPage());
    }

    /**
     * Check if isPaginable returns expected value.
     */
    public function testIsPaginable()
    {
        $pager = new PagerService(new CountAdapter($this->getDummyData()), ['limit' => 6]);
        $this->assertFalse($pager->isPaginable());
        $pager = new PagerService(new CountAdapter($this->getDummyData()), ['limit' => 3]);
        $this->assertTrue($pager->isPaginable());
    }

    /**
     * Data provider for testGetPages().
     *
     * @return array
     */
    public function getPagesData()
    {
        $out = [];
        // Case #0 only one page.
        $out[] = [6, [1]];
        // Case #1 split up into two pages.
        $out[] = [4, [1, 2]];
        // Case #2 one item per page.
        $out[] = [1, [1, 2, 3, 4, 5]];

        return $out;
    }

    /**
     * Check if get pages return an expected range of pages.
     *
     * @param int   $limit
     * @param array $expectedRange
     *
     * @dataProvider getPagesData()
     */
    public function testGetPages($limit, $expectedRange)
    {
        $pager = new PagerService(new CountAdapter($this->getDummyData()), ['limit' => $limit]);
        $this->assertEquals($expectedRange, $pager->getPages());
    }

    /**
     * Check if adapter returned is correct.
     */
    public function testGetAdapter()
    {
        $adapter = new CountAdapter([$this->getDummyData()]);
        $pager = new PagerService($adapter, []);
        $this->assertEquals($adapter, $pager->getAdapter());
    }
}
