<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filters\Range;

use ONGR\FilterManagerBundle\Filters\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use ONGR\FilterManagerBundle\Test\AbstractFilterManagerResultsTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for date range filter.
 */
class DateRangeTest extends AbstractFilterManagerResultsTest
{
    /**
     * @return array
     */
    public function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'date' => '2001-09-11T00:00:00+0000',
                    ],
                    [
                        '_id' => 2,
                        'date' => '2002-09-11T00:00:00+0000',
                    ],
                    [
                        '_id' => 3,
                        'date' => '2003-09-11T00:00:00+0000',
                    ],
                    [
                        '_id' => 4,
                        'date' => '2004-09-11T00:00:00+0000',
                    ],
                    [
                        '_id' => 5,
                        'date' => '2005-09-11T00:00:00+0000',
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTestResultsData()
    {
        $out = [];

        $managers = $this->getFilterManager();

        // Case #0 no active filter.
        $out[] = [
            new Request([]),
            ['1', '2', '3', '4', '5'],
            false,
            $managers,
        ];

        // Case #1 Filter is active and in the middle of available range.
        $out[] = [
            new Request(['date_range' => '2003-01-01;2005-12-22']),
            ['3', '4', '5'],
            false,
            $managers,
        ];

        // Case #2 Range goes from beginning.
        $out[] = [
            new Request(['date_range' => '2001-01-01;2003-12-22']),
            ['1', '2', '3'],
            false,
            $managers,
        ];

        // Case #3 Range goes to end.
        $out[] = [
            new Request(['date_range' => '2003-01-03;2005-12-22']),
            ['3', '4', '5'],
            false,
            $managers,
        ];

        return $out;
    }

    /**
     * Check if view data returned is correct.
     *
     * @param Request $request     Http request.
     * @param array   $ids         Array of document ids to assert.
     * @param bool    $assertOrder Set true if order of results lso should be asserted.
     * @param array   $managers    Set of filter managers to test.
     *
     * @dataProvider getTestResultsData()
     */
    public function testViewData(Request $request, $ids, $assertOrder = false, $managers = [])
    {
        foreach ($managers as $filter => $filterManager) {
            /** @var RangeAwareViewData $viewData */
            $viewData = $filterManager->execute($request)->getFilters()[$filter];

            $this->assertInstanceOf('ONGR\FilterManagerBundle\Filters\ViewData\RangeAwareViewData', $viewData);

            $this->assertEquals(
                date_create_from_format(\DateTime::ISO8601, '2001-09-11T00:00:00+0000'),
                $viewData->getMinBounds()
            );

            $this->assertEquals(
                date_create_from_format(\DateTime::ISO8601, '2005-09-11T00:00:00+0000'),
                $viewData->getMaxBounds()
            );
        }
    }

    /**
     * Returns filter managers.
     *
     * @return FiltersManager[]
     */
    protected function getFilterManager()
    {
        return [
            'date' => self::createClient()->getContainer()->get('ongr_filter_manager.range_filters'),
        ];
    }

    /**
     * This method asserts if search request gives expected results.
     *
     * @param Request $request     Http request.
     * @param array   $ids         Array of document ids to assert.
     * @param bool    $assertOrder Set true if order of results lso should be asserted.
     * @param array   $managers    Set of filter managers to test.
     *
     * @dataProvider getTestResultsData()
     */
    public function testResults(Request $request, $ids, $assertOrder = false, $managers = [])
    {
        foreach ($managers as $filterManager) {
            $actual = array_map(
                [$this, 'fetchDocumentId'],
                iterator_to_array($filterManager->execute($request)->getResult())
            );

            if (!$assertOrder) {
                sort($actual);
                sort($ids);
            }

            $this->assertEquals($ids, $actual);
        }
    }
}
