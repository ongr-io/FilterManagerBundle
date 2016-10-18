<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Functional\Filter\Widget\Dynamic;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Filter\ViewData\AggregateViewData;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Dynamic\DynamicAggregate;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class DynamicAggregateTest extends AbstractElasticsearchTestCase
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
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'USA',
                            ],
                            [
                                'name' => 'Color',
                                'value' => 'Green',
                            ],
                            [
                                'name' => 'Condition',
                                'value' => 'Excelent',
                            ],
                            [
                                'name' => 'Group',
                                'value' => 'Accessories',
                            ]
                        ]
                    ],
                    [
                        '_id' => 2,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'Germany',
                            ],
                            [
                                'name' => 'Condition',
                                'value' => 'Excelent',
                            ],
                            [
                                'name' => 'Group',
                                'value' => 'Accessories',
                            ]
                        ]
                    ],
                    [
                        '_id' => 3,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'Lithuania',
                            ],
                            [
                                'name' => 'Color',
                                'value' => 'Green',
                            ]
                        ]
                    ],
                    [
                        '_id' => 4,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'China',
                            ],
                            [
                                'name' => 'Condition',
                                'value' => 'Fair',
                            ]
                        ]
                    ],
                    [
                        '_id' => 5,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'USA',
                            ],
                            [
                                'name' => 'Color',
                                'value' => 'Red',
                            ]
                        ]
                    ],
                    [
                        '_id' => 6,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'USA',
                            ],
                            [
                                'name' => 'Condition',
                                'value' => 'Good',
                            ]
                        ]
                    ],
                    [
                        '_id' => 7,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'China',
                            ],
                            [
                                'name' => 'Condition',
                                'value' => 'Good',
                            ],
                            [
                                'name' => 'Group',
                                'value' => 'Accessories',
                            ]
                        ]
                    ],
                    [
                        '_id' => 8,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'Germany',
                            ],
                            [
                                'name' => 'Color',
                                'value' => 'Black',
                            ],
                            [
                                'name' => 'Group',
                                'value' => 'Maintenance',
                            ]
                        ]
                    ],
                    [
                        '_id' => 9,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'China',
                            ],
                            [
                                'name' => 'Group',
                                'value' => 'Utilities',
                            ]
                        ]
                    ],
                    [
                        '_id' => 10,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'China',
                            ],
                            [
                                'name' => 'Color',
                                'value' => 'Red',
                            ],
                            [
                                'name' => 'Condition',
                                'value' => 'Fair',
                            ]
                        ]
                    ],
                    [
                        '_id' => 11,
                        'attributes' => [
                            [
                                'name' => 'Made in',
                                'value' => 'Germany',
                            ],
                            [
                                'name' => 'Group',
                                'value' => 'Utilities',
                            ]
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * Data provider for testChoices().
     *
     * @return array
     */
    public function testDataProvider()
    {
        $out = [];

        // Case #0, without any request parameters.
        $out[] = [
            'request' => new Request(),
            'expectedChoices' => [
                'Color' => [
                    'Green' => 2,
                    'Red' => 2,
                    'Black' => 1,
                ],
                'Made in' => [
                    'USA' => 3,
                    'China' => 4,
                    'Germany' => 3,
                    'Lithuania' => 1,
                ],
                'Condition' => [
                    'Excelent' => 2,
                    'Fair' => 2,
                    'Good' => 2,
                ],
                'Group' => [
                    'Accessories' => 3,
                    'Utilities' => 2,
                    'Maintenance' => 1,
                ],
            ],
            'filter' => 'dynamic_aggregate_filter'
        ];

        // Case #0, with color red
        $out[] = [
            'request' => new Request(['dynamic_aggregate' => ['Color' => 'Red']]),
            'expectedChoices' => [
                'Color' => [
                    'Green' => 2,
                    'Red' => 2,
                    'Black' => 1,
                ],
                'Made in' => [
                    'USA' => 1,
                    'China' => 1,
                ],
                'Condition' => [
                    'Fair' => 1,
                ]
            ],
            'filter' => 'dynamic_aggregate_filter'
        ];

        // Case #0, with color red, with zero choices
        $out[] = [
            'request' => new Request(['zero_aggregate' => ['Color' => 'Red']]),
            'expectedChoices' => [
                'Color' => [
                    'Green' => 2,
                    'Red' => 2,
                    'Black' => 1,
                ],
                'Made in' => [
                    'USA' => 1,
                    'China' => 1,
                    'Lithuania' => 0,
                    'Germany' => 0,
                ],
                'Condition' => [
                    'Fair' => 1,
                    'Good' => 0,
                    'Excelent' => 0,
                ],
                'Group' => [
                    'Accessories' => 0,
                    'Utilities' => 0,
                    'Maintenance' => 0,
                ],
            ],
            'filter' => 'dynamic_aggregate_with_zero_choice_filter'
        ];

        return $out;
    }

    /**
     * Check if choices are formed as expected.
     *
     * @param Request $request
     * @param array $expectedChoices
     * @param string $filter
     *
     * @dataProvider testDataProvider
     */
    public function testChoices(Request $request, $expectedChoices, $filter)
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('dynamic_filters'));

        /** @var AggregateViewData $result */
        $result = $manager->handleRequest($request)->getFilters()[$filter];

        $actualChoices = $this->extractActualChoices($result);

        $this->assertTrue($result instanceof AggregateViewData);
        $this->assertEquals($expectedChoices, $actualChoices);
    }

    /**
     * Extracts actualChoices array with the right
     * configuration from the result
     *
     * @param AggregateViewData $result
     *
     * @return array
     */
    private function extractActualChoices($result)
    {
        $actualChoices = [];

        foreach ($result->getItems() as $choiceViewData) {
            foreach ($choiceViewData->getChoices() as $choice) {
                $actualChoices[$choiceViewData->getName()][$choice->getLabel()] = $choice->getCount();
            }
        }

        return $actualChoices;
    }
}
