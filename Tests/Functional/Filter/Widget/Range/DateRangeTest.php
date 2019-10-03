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

use App\Document\Product;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for date range filter.
 */
class DateRangeTest extends AbstractElasticsearchTestCase
{
    /**
     * @return array
     */
    public function getDataArray()
    {
        return [
            Product::class => [
                [
                    '_id' => 1,
                    'date' => '2001-09-11',
                    'color' => 'red',
                ],
                [
                    '_id' => 2,
                    'date' => '2002-09-12',
                    'color' => 'blue',
                ],
                [
                    '_id' => 3,
                    'date' => '2003-09-11',
                    'color' => 'blue',
                ],
                [
                    '_id' => 4,
                    'date' => '2004-09-11',
                    'color' => 'blue',
                ],
                [
                    '_id' => 5,
                    'date' => '2005-10-11',
                    'color' => 'red',
                ],
            ],
        ];
    }


    /**
     * Data provider.
     *
     * @return array
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0
        $out[] = [
            [3,2],
            ['date_range' => '2002-09-11;2004-09-11'],
        ];

        // Case #1
        $out[] = [
            [3,2],
            ['date_range' => '1031788700000;1063239400000'],
        ];

        // Case #1
        $out[] = [
            [3,2],
            ['date_range' => '2002-09-11;1063239400000'],
        ];

        return $out;
    }

    /**
     * Check if choices are filtered and sorted as expected.
     *
     * @param array $expectedChoices
     * @param array $query
     *
     * @dataProvider getTestResultsData()
     */
    public function testFilter($expectedChoices, $query = [])
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('range'));
        $result = $manager->handleRequest(new Request($query))->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals(sort($expectedChoices), sort($actual));
    }

    public function testBoundsFormation()
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('range'));
        $result = $manager->handleRequest(new Request())->getFilters()['date_range_filter'];

        $this->assertEquals('2001-09-11', $result->getMinBounds()->format('Y-m-d'));
        $this->assertEquals('2005-10-11', $result->getMaxBounds()->format('Y-m-d'));

        $result = $manager->handleRequest(new Request(['limit' => 'blue']))->getFilters()['date_range_filter'];

        $this->assertEquals('2001-09-11', $result->getMinBounds()->format('Y-m-d'));
        $this->assertEquals('2005-10-11', $result->getMaxBounds()->format('Y-m-d'));
    }

    protected function setUp()
    {
        $this->getIndex(Product::class);
    }
}
