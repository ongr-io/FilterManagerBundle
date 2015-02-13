<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\DependencyInjection;

use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Unit tests for extension class.
 */
class ONGRFilterManagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testRelations.
     *
     * @return array
     */
    public function testRelationsData()
    {
        $out = [];

        // Case #0 no relations set.
        $out[] = [$this->getDummyConfig(), []];

        // Case #1, single search include relation.
        $relations = [
            'search' => [
                'include' => ['firstItem', 'secondItem'],
            ],
        ];
        $expectedDefinition = new Definition(
            'ONGR\FilterManagerBundle\Relations\IncludeRelation',
            [['firstItem', 'secondItem']]
        );
        $out[] = [$this->getDummyConfig($relations), [['setSearchRelation', [$expectedDefinition]]]];

        // Case #2, single reset exclude relation.
        $relations = [
            'reset' => [
                'exclude' => ['firstItem', 'secondItem'],
            ],
        ];
        $expectedDefinition = new Definition(
            'ONGR\FilterManagerBundle\Relations\ExcludeRelation',
            [['firstItem', 'secondItem']]
        );
        $out[] = [$this->getDummyConfig($relations), [['setResetRelation', [$expectedDefinition]]]];

        // Case #3, reset include and search exclude relation.
        $relations = [
            'reset' => [
                'include' => ['resetItem'],
            ],
            'search' => [
                'exclude' => ['searchItem'],
            ],
        ];
        $expectedResetDefinition = new Definition(
            'ONGR\FilterManagerBundle\Relations\IncludeRelation',
            [['resetItem']]
        );
        $resetCall = ['setResetRelation', [$expectedResetDefinition]];
        $expectedSearchDefinition = new Definition(
            'ONGR\FilterManagerBundle\Relations\ExcludeRelation',
            [['searchItem']]
        );
        $searchCall = ['setSearchRelation', [$expectedSearchDefinition]];
        $out[] = [$this->getDummyConfig($relations), [$searchCall, $resetCall]];

        return $out;
    }

    /**
     * Check if relations are set as expected.
     *
     * @param array $configuration
     * @param array $expectedDefinitions
     *
     * @dataProvider testRelationsData()
     */
    public function testRelations(array $configuration, array $expectedDefinitions)
    {
        $containerBuilder = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $extension->load($configuration, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('ongr_filter_manager.filter.test_sorting'));

        $definition = $containerBuilder->getDefinition('ongr_filter_manager.filter.test_sorting');

        if (!empty($expectedDefinitions)) {
            $methodCalls = $definition->getMethodCalls();
            $relationCalls = array_splice($methodCalls, 2);
            $this->assertEquals(count($expectedDefinitions), count($relationCalls));
            $this->assertEquals($expectedDefinitions, $relationCalls);
        } else {
            $this->assertEquals(2, count($definition->getMethodCalls()));
        }
    }

    /**
     * Check if expected method calls are added to the filter definition.
     */
    public function testMethodCalls()
    {
        $config = $this->getDummyConfig([]);
        $pagerConfig = [
            'test_pager' => [
                'request_field' => 'page',
                'field' => 'page_field',
                'count_per_page' => 10,
                'max_pages' => 8,
            ],
        ];
        $config['ongr_filter_manager']['filters']['pager'] = $pagerConfig;
        $containerBuilder = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('ongr_filter_manager.filter.test_pager'));
        $definition = $containerBuilder->getDefinition('ongr_filter_manager.filter.test_pager');

        $expectedMethods = [
            [
                'setRequestField',
                ['page'],
            ],
            [
                'setField',
                ['page_field'],
            ],
            [
                'setCountPerPage',
                ['10'],
            ],
            [
                'setMaxPages',
                ['8'],
            ],
        ];

        $this->assertEquals($expectedMethods, $definition->getMethodCalls());
    }

    /**
     * Tests if extension runs with empty configuration without exceptions.
     */
    public function testWithEmptyConfiguration()
    {
        $config = [];
        $container = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $extension->load($config, $container);

        foreach ($container->getDefinitions() as $id => $definition) {
            $this->assertNotRegExp('/^ongr_filter_manager\.filter\.(\D+)/', $id);
        }
    }

    /**
     * Returns dummy configuration for testing.
     *
     * @param array $relations
     *
     * @return array
     */
    protected function getDummyConfig($relations = [])
    {
        return [
            'ongr_filter_manager' => [
                'managers' => [
                    'test' => [
                        'repository' => 'test',
                        'filters' => [
                            'test_sorting',
                        ],
                    ],
                ],
                'filters' => [
                    'sort' => [
                        'test_sorting' =>
                        [
                            'relations' => $relations,
                            'request_field' => 'sort',
                            'choices' => [
                                ['label' => 'test', 'field' => 'test'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
