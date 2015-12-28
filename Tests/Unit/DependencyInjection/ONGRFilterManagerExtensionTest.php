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
    public function getTestRelationsData()
    {
        $out = [];
        
        $mock0 = $this->getMock('Symfony\Component\DependencyInjection\Definition');

        // Case #0 no relations set.
        $out[] = [$this->getDummyConfig(), $mock0];
        
        // Case #1, single search include relation.
        $relations = [
            'search' => [
                'include' => ['firstItem', 'secondItem'],
            ],
        ];
        
        $mock1 = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mock1
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                'setSearchRelation',
                $this->callback(
                    function ($definition) {
                        $definition = reset($definition);
                        
                        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $definition);
                        $this->assertEquals(
                            'ONGR\FilterManagerBundle\Relation\IncludeRelation',
                            $definition->getClass()
                        );
                        $this->assertEquals(
                            [
                                ['firstItem', 'secondItem'],
                            ],
                            $definition->getArguments()
                        );
                        
                        return true;
                    }
                )
            );
        
        $out[] = [$this->getDummyConfig($relations), $mock1];

        // Case #2, single reset exclude relation.
        $relations = [
            'reset' => [
                'exclude' => ['firstItem', 'secondItem'],
            ],
        ];
        $mock2 = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mock2
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                'setResetRelation',
                $this->callback(
                    function ($definition) {
                        $definition = reset($definition);

                        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $definition);
                        $this->assertEquals(
                            'ONGR\FilterManagerBundle\Relation\ExcludeRelation',
                            $definition->getClass()
                        );
                        $this->assertEquals(
                            [
                                ['firstItem', 'secondItem'],
                            ],
                            $definition->getArguments()
                        );

                        return true;
                    }
                )
            );
        $out[] = [$this->getDummyConfig($relations), $mock2];

        // Case #3, reset include and search exclude relation.
        $relations = [
            'reset' => [
                'include' => ['resetItem'],
            ],
            'search' => [
                'exclude' => ['searchItem'],
            ],
        ];

        $mock3 = $this->getMock('Symfony\Component\DependencyInjection\Definition');
        $mock3
            ->expects($this->at(0))
            ->method('addMethodCall')
            ->with(
                'setSearchRelation',
                $this->callback(
                    function ($definition) {
                        $definition = reset($definition);

                        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $definition);
                        $this->assertEquals(
                            'ONGR\FilterManagerBundle\Relation\ExcludeRelation',
                            $definition->getClass()
                        );
                        $this->assertEquals(
                            [
                                ['searchItem'],
                            ],
                            $definition->getArguments()
                        );

                        return true;
                    }
                )
            );
        $mock3
            ->expects($this->at(1))
            ->method('addMethodCall')
            ->with(
                'setResetRelation',
                $this->callback(
                    function ($definition) {
                        $definition = reset($definition);

                        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Definition', $definition);
                        $this->assertEquals(
                            'ONGR\FilterManagerBundle\Relation\IncludeRelation',
                            $definition->getClass()
                        );
                        $this->assertEquals(
                            [
                                ['resetItem'],
                            ],
                            $definition->getArguments()
                        );

                        return true;
                    }
                )
            );
        
        
        $out[] = [$this->getDummyConfig($relations), $mock3];

        return $out;
    }

    /**
     * Check if relations are set as expected.
     *
     * @param array $configuration
     * @param array $definitionMock
     *
     * @dataProvider getTestRelationsData()
     */
    public function testRelations(array $configuration, $definitionMock)
    {
        $factoryMock = $this->getMock('ONGR\FilterManagerBundle\DependencyInjection\Filter\PagerFilterFactory');
        $factoryMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn('sort');
        $factoryMock
            ->expects($this->once())
            ->method('setConfiguration')
            ->willReturnSelf();
        $factoryMock
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definitionMock);

        $containerBuilder = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $extension->addFilterFactory($factoryMock);
        $extension->load($configuration, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('ongr_filter_manager.filter.test_sorting'));
    }

    /**
     * Check if expected method calls are added to the filter definition.
     */
    public function testMethodCalls()
    {
        $config = $this->getDummyConfig();
        $filterConfig = [
            'request_field' => 'page',
            'field' => 'page_field',
            'count_per_page' => 10,
            'max_pages' => 8,
            'tags' => [],
        ];

        $config['ongr_filter_manager']['filters'] = [
            'pager' => [
                'test_pager' => $filterConfig,
            ],
        ];
        $containerBuilder = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        
        $factoryMock = $this->getMock('ONGR\FilterManagerBundle\DependencyInjection\Filter\PagerFilterFactory');
        $factoryMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn('pager');
        $factoryMock
            ->expects($this->once())
            ->method('setConfiguration')
            ->with($filterConfig)
            ->willReturnSelf();
        $factoryMock
            ->expects($this->once())
            ->method('getDefinition')
            ->willReturn($this->getMock('Symfony\Component\DependencyInjection\Definition'));
        
        $extension->addFilterFactory($factoryMock);
        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('ongr_filter_manager.filter.test_pager'));
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
     * Tests if exception is thrown when filter names are duplicated.
     *
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Found duplicate filter name `test_sorting` in `match` filter
     */
    public function testConfigWithDuplicateNameException()
    {
        $config = array_replace_recursive(
            $this->getDummyConfig(),
            [
                'ongr_filter_manager' => [
                    'filters' => [
                        'match' => [
                            'test_sorting' => [
                                'request_field' => 'test_q',
                                'field' => 'test_title',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $container = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $extension->load($config, $container);
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
                        'test_sorting' => [
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
