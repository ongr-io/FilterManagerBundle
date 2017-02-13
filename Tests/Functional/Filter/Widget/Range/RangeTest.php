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

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for range filter.
 */
class RangeTest extends AbstractElasticsearchTestCase
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
                        'color' => 'red',
                        'manufacturer' => 'a',
                        'price' => 1,
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                        'price' => 2,
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                        'price' => 3,
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'price' => 4,
                    ],
                    [
                        '_id' => 5,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'price' => 4.2,
                    ],
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
            [5,4,3,2,1]
        ];

        // Case #1
        $out[] = [
            [3,2],
            ['price' => '1;3.5'],
        ];

        // Case #2
        $out[] = [
            [3,2,1],
            ['inclusive_range' => '1;3.5'],
        ];

        // Case #3
        $out[] = [
            [5,4,3,2,1],
            ['price' => '1'],
        ];

        // Case #4
        $out[] = [
            [5,4,3,2,1],
            ['price' => '1'],
        ];

        // Case #4
        $out[] = [
            [5,4,3],
            ['custom_range' => '2'],
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

        $this->assertEquals($expectedChoices, $actual);
    }

    public function testFilterWithRelatedSearch()
    {
        /** @var FilterManager $manager */
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('range'));
        $result = $manager->handleRequest(new Request(['limit' => 'red']));
        $priceViewData = $result->getFilters()['price_range'];

        $this->assertEquals(1, floor($priceViewData->getMinBounds()));
        $this->assertEquals(5, ceil($priceViewData->getMaxBounds()));
    }
}
