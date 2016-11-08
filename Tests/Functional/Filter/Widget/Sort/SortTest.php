<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Sort;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\HttpFoundation\Request;

class SortTest extends AbstractElasticsearchTestCase
{
    /**
     * @return array
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'color' => 'red',
                        'manufacturer' => 'a',
                        'stock' => 5,
                        'price' => 1,
                        // Average = 3, sum = 15.
                        'items' => [1, 2, 3, 4, 5],
                        'words' => ['one', 'two', 'three', 'alfa', 'beta'],
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                        'stock' => 6,
                        'price' => 3,
                        // Average = 7.2, sum = 36.
                        'items' => [2, 12, 3, 14, 5],
                        'words' => ['eta', 'geta', 'zeta', 'beta', 'deta'],
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                        'stock' => 2,
                        'price' => 3,
                        // Average = 3.2, sum = 16.
                        'items' => [5, 4, 3, -12, 16],
                        'words' => ['vienas', 'du', 'trys', 'keturi', 'penki'],
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'stock' => 7,
                        'price' => 4,
                        // Average = 6, sum = 30.
                        'items' => [1, -2, 30, -4, 5],
                        'words' => ['eins', 'zwei', 'drei', 'fier', 'funf'],
                    ],
                ],
            ],
        ];
    }


    /**
     * Data provider for testFilter().
     *
     * @return array
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0: ascending sorting.
        $out[] = [
            ['3', '1', '2', '4'],
            ['sort' => 'asc'],
        ];

        // Case #1
        $out[] = [
            ['4', '2', '1', '3'],
            ['sort' => 'desc'],
        ];

        // Case #2
        $out[] = [
            ['4', '2', '3', '1'],
            ['sort' => 'desc_price_desc_stock'],
        ];

        // Case #3
        $out[] = [
            ['1', '2', '3', '4'],
            ['sort' => 'asc_price_desc_stock'],
        ];

        // Case #4
        $out[] = [
            ['1', '3', '2', '4'],
            ['sort' => 'asc_price_asc_stock'],
        ];

        // Case #5
        $out[] = [
            ['1', '3', '4', '2'],
            ['sort' => 'asc_items_sum_mode'],
        ];

        // Case #6
        $out[] = [
            ['3', '1', '2', '4'],
            [],
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

        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('sorting'));
        $result = $manager->handleRequest(new Request($query))->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals($expectedChoices, $actual);
    }
}
