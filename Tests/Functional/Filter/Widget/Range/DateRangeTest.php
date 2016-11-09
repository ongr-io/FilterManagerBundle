<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Range;

use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Filter\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Search\FilterManager;
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

        // Case #0 no active filter.
        $out[] = [
            new Request([]),
            ['1', '2', '3', '4', '5'],
            false,
        ];

        // Case #1 Filter is active and in the middle of available range.
        $out[] = [
            new Request(['date_range' => '2003-01-01;2005-12-22']),
            ['3', '4', '5'],
            false,
        ];

        // Case #2 Range goes from beginning.
        $out[] = [
            new Request(['date_range' => '2001-01-01;2003-12-22']),
            ['1', '2', '3'],
            false,
        ];

        // Case #3 Range goes to end.
        $out[] = [
            new Request(['date_range' => '2003-01-03;2005-12-22']),
            ['3', '4', '5'],
            false,
        ];

        return $out;
    }

    /**
     * Returns filter managers.
     *
     * @return FilterManager
     */
    protected function getFilterManager()
    {
        /** @var FilterManager $manager */
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('range_filters'));

        return $manager;
    }

    /**
     * Check if view data returned is correct.
     *
     * @param Request $request     Http request.
     * @param array   $ids         Array of document ids to assert.
     * @param bool    $assertOrder Set true if order of results lso should be asserted.
     *
     * @dataProvider getTestResultsData()
     */
    public function testViewData(Request $request, $ids, $assertOrder = false)
    {
        /** @var RangeAwareViewData $viewData */
        $viewData = $this->getFilterManager()->handleRequest($request)->getFilters()['date'];

        $this->assertInstanceOf('ONGR\FilterManagerBundle\Filter\ViewData\RangeAwareViewData', $viewData);

        $this->assertEquals(
            date_create_from_format(\DateTime::ISO8601, '2001-09-11T00:00:00+0000'),
            $viewData->getMinBounds()
        );

        $this->assertEquals(
            date_create_from_format(\DateTime::ISO8601, '2005-09-11T00:00:00+0000'),
            $viewData->getMaxBounds()
        );
    }

    /**
     * This method asserts if search request gives expected results.
     *
     * @param Request $request     Http request.
     * @param array   $ids         Array of document ids to assert.
     * @param bool    $assertOrder Set true if order of results lso should be asserted.
     *
     * @dataProvider getTestResultsData()
     */
    public function testResults(Request $request, $ids, $assertOrder = false)
    {
        $actual = array_map(
            [$this, 'fetchDocumentId'],
            iterator_to_array($this->getFilterManager()->handleRequest($request)->getResult())
        );

        if (!$assertOrder) {
            sort($actual);
            sort($ids);
        }

        $this->assertEquals($ids, $actual);
    }
}
