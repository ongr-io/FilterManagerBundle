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

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Filter\ViewData\AggregateViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Dynamic\MultiDynamicAggregate;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class MultiDynamicAggregateTest extends AbstractElasticsearchTestCase
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
                                'value' => 'Excellent',
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
                                'value' => 'Excellent',
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
    public function getChoicesSortData()
    {
        $out = [];

        // Case #0, without any request parameters.
        $out[] = [
            'choices' => [],
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
                    'Excellent' => 2,
                    'Fair' => 2,
                    'Good' => 2,
                ],
                'Group' => [
                    'Accessories' => 3,
                    'Utilities' => 2,
                    'Maintenance' => 1,
                ],
            ],
            'filter' => 'multi_dynamic_aggregate_filter'
        ];
        // Case #1, 2 parameters from different groups.
        $out[] = [
            'choices' => [
                'multi_dynamic_aggregate' => [
                    'Made in' => ['China'],
                    'Group' => ['Accessories'],
                ]
            ],
            'expectedChoices' => [
                'Made in' => [
                    'USA' => 1,
                    'China' => 1,
                    'Germany' => 1,
                ],
                'Condition' => [
                    'Good' => 1,
                ],
                'Group' => [
                    'Accessories' => 1,
                    'Utilities' => 1,
                ],
            ],
            'filter' => 'multi_dynamic_aggregate_filter'
        ];
        // Case #2, same group parameters.
        $out[] = [
            'choices' => [
                'multi_dynamic_aggregate' => [
                    'Made in' => ['China', 'USA'],
                    'Condition' => ['Good'],
                ]
            ],
            'expectedChoices' => [
                'Made in' => [
                    'USA' => 1,
                    'China' => 1,
                ],
                'Condition' => [
                    'Good' => 2,
                    'Excellent' => 1,
                    'Fair' => 2,
                ],
                'Group' => [
                    'Accessories' => 1,
                ],
            ],
            'filter' => 'multi_dynamic_aggregate_filter'
        ];

        return $out;
    }

    /**
     * Check if choices are formed as expected.
     *
     * @param array $choices
     * @param array $expectedChoices
     * @param string $filter
     *
     * @dataProvider getChoicesSortData()
     */
    public function testChoices($choices, $expectedChoices, $filter)
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('dynamic_filters'));

        /** @var AggregateViewData $result */
        $result = $manager->handleRequest(new Request($choices))->getFilters()[$filter];
        $this->assertTrue($result instanceof AggregateViewData);
        $this->assertEquals($expectedChoices, $this->extractActualChoices($result));
    }

//    /**
//     * Tests if the documents are filtered as expected
//     */
//    public function testFiltering()
//    {
//        /** @var DocumentIterator $result */
//        $result = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('dynamic_filters'))
//            ->handleRequest(new Request(
//                ['multi_dynamic_aggregate' => ['Made in' => ['China', 'USA'], 'Condition' => ['Good']]]
//            ))->getResult();
//
//        $this->assertEquals(2, $result->count());
//    }

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
