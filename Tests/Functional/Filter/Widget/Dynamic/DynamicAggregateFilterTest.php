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
use ONGR\FilterManagerBundle\Filter\ViewData\AggregateViewData;
use Symfony\Component\HttpFoundation\Request;

class DynamicAggregateFilterTest extends AbstractElasticsearchTestCase
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
     * Check if choices are sorted as expected using configuration settings.
     */
    public function testChoicesConfiguration()
    {
        /** @var AggregateViewData $result */
        $result = $this->getContainer()->get('ongr_filter_manager.dynamic_filters')
            ->handleRequest(new Request())->getFilters()['dynamic_aggregate'];
        $this->assertTrue($result instanceof AggregateViewData);

        $actualChoices = [];
        $expectedChoices = [
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
        ];

        $items = $result->getItems();

        foreach ($items as $choiceViewData) {
            foreach ($choiceViewData->getChoices() as $choice) {
                $actualChoices[$choiceViewData->getName()][$choice->getLabel()] = $choice->getCount();
            }
        }

        $this->assertEquals($expectedChoices, $actualChoices);
    }
}
