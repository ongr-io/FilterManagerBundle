<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Choice;

use App\Document\Product;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use Symfony\Component\HttpFoundation\Request;

class MultiTermChoiceTest extends AbstractElasticsearchTestCase
{
    /**
     * @return array
     */
    protected function getDataArray()
    {
        return [
            Product::class => [
                [
                    '_id' => 1,
                    'color' => 'red',
                    'manufacturer' => 'a',
                    'sku' => 'foo',
                    'title' => 'm1',
                ],
                [
                    '_id' => 2,
                    'color' => 'blue',
                    'manufacturer' => 'a',
                    'sku' => 'foo',
                    'title' => 'm2',
                ],
                [
                    '_id' => 3,
                    'color' => 'red',
                    'manufacturer' => 'b',
                    'sku' => 'foo',
                    'title' => 'm3',
                ],
                [
                    '_id' => 4,
                    'color' => 'blue',
                    'manufacturer' => 'b',
                    'sku' => 'foo',
                    'title' => 'm4',
                ],
                [
                    '_id' => 5,
                    'color' => 'green',
                    'manufacturer' => 'b',
                    'sku' => 'acme',
                    'title' => 'm5',
                ],
                [
                    '_id' => 6,
                    'color' => 'blue',
                    'manufacturer' => 'a',
                    'sku' => 'acme',
                    'title' => 'm6',
                ],
                [
                    '_id' => 7,
                    'color' => 'yellow',
                    'manufacturer' => 'a',
                    'sku' => 'bar',
                    'title' => 'm7',
                ],
                [
                    '_id' => 8,
                    'color' => 'red',
                    'manufacturer' => 'a',
                    'sku' => 'bar',
                    'title' => 'm8',
                ],
                [
                    '_id' => 9,
                    'color' => 'blue',
                    'manufacturer' => 'c',
                    'sku' => 'bar',
                    'title' => 'm9',
                ],
                [
                    '_id' => 10,
                    'color' => 'red',
                    'manufacturer' => 'c',
                    'sku' => 'foo',
                    'title' => 'm10',
                ],
                [
                    '_id' => 11,
                    'color' => 'blue',
                    'manufacturer' => 'a',
                    'sku' => 'bar',
                    'title' => 'm11',
                ],
            ],
        ];
    }

    /**
     * Data provider for testChoicesFilter().
     *
     * @return array
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0, sorted in default acceding
        $out[] = [
            [
                'red' => 4,
                'blue' => 5,
                'green' => 1,
                'yellow' => 1,
            ],
            'mc_filter'
        ];

        // Case #1
        $out[] = [
            [
                'red' => 1,
                'blue' => 1,
                'green' => 1,
            ],
            'mc_filter',
            ['manufacturer' => 'b']
        ];

        // Case #2
        $out[] = [
            [
                'red' => 2,
                'blue' => 3,
                'yellow' => 1,
            ],
            'mc_filter',
            ['manufacturer' => 'a']
        ];

        // Case #3
        $out[] = [
            [
                'blue' => 3,
                'red' => 2,
                'yellow' => 1,
            ],
            'mc_filter',
            ['manufacturer' => 'a']
        ];

        // Case #4
        $out[] = [
            [
                'blue' => 4,
                'red' => 3,
                'green' => 1,
                'yellow' => 1,
            ],
            'mc_filter_zero',
            ['manufacturer' => ['a', 'b']]
        ];

        // Case #5
        $out[] = [
            [
                'a' => 6,
                'b' => 3,
                'c' => 2,
            ],
            'mc_filter_man_priority_blue',
            []
        ];

        // Case #6
        $out[] = [
            [
                'a' => 2,
                'b' => 2,
                'c' => 1,
            ],
            'mc_filter_man',
            ['color' => ['red', 'green']]
        ];

        return $out;
    }

    /**
     * Check if choices are filtered and sorted as expected.
     *
     * @param array $expectedChoices
     * @param string $filter
     * @param array $query
     *
     * @dataProvider getTestResultsData()
     */
    public function testChoicesFilter($expectedChoices, $filter, $query = [])
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('multi_choices'));

        /** @var ChoicesAwareViewData $result */
        $result = $manager->handleRequest(new Request($query))->getFilters()[$filter];

        $actualChoices = [];

        foreach ($result->getChoices() as $choice) {
            $actualChoices[$choice->getLabel()] = $choice->getCount();
        }

        $this->assertEquals($expectedChoices, $actualChoices);
    }

    protected function setUp()
    {
        $this->getIndex(Product::class);
    }
}
