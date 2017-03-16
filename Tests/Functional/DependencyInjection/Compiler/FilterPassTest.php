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

class FilterPassTest extends AbstractElasticsearchTestCase
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
                        'color' => 'blue',
                        'active' => true,
                        'price' => 1.5,
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'active' => false,
                        'price' => 2.5,
                    ],
                    [
                        '_id' => 3,
                        'color' => 'blue',
                        'active' => true,
                        'price' => 3.5,
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'active' => false,
                        'price' => 4.5,
                    ],
                    [
                        '_id' => 5,
                        'color' => 'red',
                        'active' => true,
                        'price' => 2.5,
                    ],
                    [
                        '_id' => 6,
                        'color' => 'red',
                        'active' => false,
                        'price' => 3.5,
                    ],
                    [
                        '_id' => 7,
                        'color' => 'red',
                        'active' => true,
                        'price' => 4.5,
                    ],
                    [
                        '_id' => 8,
                        'color' => 'red',
                        'active' => false,
                        'price' => 5.5,
                    ],
                    [
                        '_id' => 9,
                        'color' => 'green',
                        'active' => true,
                        'price' => 4.5,
                    ],
                    [
                        '_id' => 10,
                        'color' => 'green',
                        'active' => false,
                        'price' => 5.5,
                    ],
                    [
                        '_id' => 11,
                        'color' => 'yellow',
                        'active' => true,
                        'price' => 6.5,
                    ],
                    [
                        '_id' => 12,
                        'color' => 'yellow',
                        'active' => false,
                        'price' => 7.5,
                    ],
                ],
            ],
        ];
    }

    /**
     * Data provider for testRelations().
     *
     * @return array
     */
    public function getTestRelationsData()
    {
        $out = [];

        // Case #0, search include relation
        $out['search_include'] = [
            new Request(['price' => '4;10']),
            'relation_search',
            'color_search_include',
            [
                'control' => [
                    'red' => 1,
                    'green' => 1,
                    'yellow' => 1,
                ],
                'related' => [
                    'blue' => 2,
                    'red' => 2,
                    'green' => 1,
                    'yellow' => 1,
                ],
            ],
        ];

        // Case #1, search exclude relation
        $out['search_exclude'] = [
            new Request(['price' => '1;5']),
            'relation_search',
            'color_search_exclude',
            [
                'control' => [
                    'blue' => 2,
                    'red' => 2,
                    'green' => 1,
                ],
                'related' => [
                    'blue' => 4,
                    'red' => 3,
                    'green' => 1,
                ],
            ],
        ];

        // Case #2, reset include relation
        $out['reset_include'] = [
            new Request(['price' => '4;10']),
            'relation_reset',
            'color_reset_include',
            [
                'control' => [
                    'price' => '4;10',
                ],
                'related' => [
                    'price' => '4;10',
                ],
            ],
        ];

        // Case #3, reset exclude relation
        $out['reset_exclude'] = [
            new Request(['price' => '1;5', 'color_reset_exclude' => 'blue']),
            'relation_reset',
            'color_reset_exclude',
            [
                'control' => [
                    'price' => '1;5',
                ],
                'related' => [],
            ],
        ];

        return $out;
    }

    /**
     * @param Request $request
     * @param $manager
     * @param $filter
     * @param array $expectedChoices
     *
     * @dataProvider getTestRelationsData()
     */
    public function testRelations(Request $request, $manager, $filter, array $expectedChoices)
    {
        $actualChoices = [];

        /** @var ChoicesAwareViewData $control */
        $control = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('relation_control'))
            ->handleRequest($request)->getFilters()['color_control'];
        /** @var ChoicesAwareViewData $related */
        $related = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId($manager))
            ->handleRequest($request)->getFilters()[$filter];

        if ($manager == 'relation_search') {
            foreach ($control->getChoices() as $choice) {
                $actualChoices['control'][$choice->getLabel()] = $choice->getCount();
            }

            foreach ($related->getChoices() as $choice) {
                $actualChoices['related'][$choice->getLabel()] = $choice->getCount();
            }
        } else {
            $actualChoices['control'] = $control->getResetUrlParameters();
            $actualChoices['related'] = $related->getResetUrlParameters();
        }

        $this->assertEquals($expectedChoices, $actualChoices);
    }
}
