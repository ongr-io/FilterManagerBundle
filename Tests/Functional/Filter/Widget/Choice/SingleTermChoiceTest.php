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

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use Symfony\Component\HttpFoundation\Request;

class SingleTermChoiceTest extends AbstractElasticsearchTestCase
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
                        'manufacturer' => 'a',
                        'sku' => 'bar',
                        'title' => 'm9',
                    ],
                    [
                        '_id' => 10,
                        'color' => 'red',
                        'manufacturer' => 'a',
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
            ],
        ];
    }

    /**
     * Check if choices are sorted as expected using configuration settings.
     */
    public function testSimpleChoice()
    {
        /** @var ChoicesAwareViewData $result */
        $result = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('single_choice'))
            ->handleRequest(new Request())->getFilters()['sc'];

        $expectedChoices = [
            'green',
            'yellow',
            'red',
            'blue',
        ];

        $actualChoices = [];

        foreach ($result->getChoices() as $choice) {
            $actualChoices[] = $choice->getLabel();
        }

        $this->assertEquals($expectedChoices, $actualChoices);
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
                'green',
                'yellow',
                'red',
                'blue',
            ],
            'sc'
        ];

        // Case #1, sorted in descending order by term, blue is prioritized.
        $out[] = [
            [
                'acme',
                'bar',
                'foo',
            ],
            'sc_zero_choices'
        ];

        // Case #2, all items prioritized, so sorting shouldn't matter.
        $out[] = [
            [
                'blue',
                'green',
            ],
            'sc_sort_term_a'
        ];

        // Case #3, sort items by count, red prioritized.
        $out[] = [
            [
                'yellow',
            ],
            'sc_sort_term_d'
        ];

        // Case #4
        $out[] = [
            [
                'blue',
                'red',
                'green',
                'yellow'
            ],
            'sc_sort_count'
        ];

        // Case #5 with selected man
        $out[] = [
            [
                'yellow',
                'red',
                'blue',
            ],
            'sc',
            ['manufacturer' => 'a']
        ];

        // Case #6 with selected man with zero choices
        $out[] = [
            [
                'acme',
                'foo',
                'bar',
            ],
            'sc_zero_choices',
            ['manufacturer' => 'a']
        ];

        // Case #7 with selected man with zero choices
        $out[] = [
            [
                'yellow',
                'red',
                'blue',
                'green',
            ],
            'sc_zero_choices_color',
            ['manufacturer' => 'a']
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
    public function testFilter($expectedChoices, $filter, $query = [])
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('single_choice'));

        /** @var ChoicesAwareViewData $result */
        $result = $manager->handleRequest(new Request($query))->getFilters()[$filter];

        $actualChoices = [];

        foreach ($result->getChoices() as $choice) {
            $actualChoices[] = $choice->getLabel();
        }

        $this->assertEquals($expectedChoices, $actualChoices);
    }
}
