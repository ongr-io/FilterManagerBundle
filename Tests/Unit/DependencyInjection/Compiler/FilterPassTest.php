<?php

/*
 * This file is part of the ONGR package.
 *
 * Copyright (c) 2014-2015 NFQ Technologies UAB
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\DependencyInjection\Compiler;

use ONGR\FilterManagerBundle\DependencyInjection\Compiler\FilterPass;
use ONGR\FilterManagerBundle\Tests\app\fixture\Acme\TestBundle\Filters\FooRange\FooRange;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FilterPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * Before a test method is run, a template method called setUp() is invoked.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->expects($this->any())->method('getParameter')->with(
            'ongr_filter_manager.filter_map'
        )->willReturn([]);

        $filterManager = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->setConstructorArgs(['ONGR\FilterManagerBundle\Search\FilterManager'])
            ->getMock();

        $filterContainer = new Definition('ONGR\FilterManagerBundle\Search\FilterContainer');
        $filterManager->expects($this->any())->method('getArgument')->willReturn(
            $filterContainer
        );

        $this->container->expects($this->any())->method('getDefinition')->with($this->anything())
            ->willReturn($filterManager);

        $filterMock = $this->getMockBuilder('ONGR\FilterManagerBundle\Search\FilterManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->expects($this->any())->method('get')->with($this->anything())
            ->will(
                $this->returnCallback(
                    function ($parameter) use ($filterManager, $filterMock) {
                        switch ($parameter) {
                            case 'ongr_filter_manager.foo_filters':
                                return $filterManager;
                            case 'ongr_filter_manager.filter.foo_filter':
                                return new FooRange('range', 'price');
                            case 'ongr_filter_manager.filter.bar_filter':
                                return $filterMock;
                            default:
                                return null;
                        }
                    }
                )
            );
    }

    /**
     * Tests filter name tag not set exception.
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testFilterNameTagNotSet()
    {
        $this->setContainerConfig(
            [],
            [
                'ongr_filter_manager.filter.foo_filter' => [
                    [],
                ],
            ]
        );

        $compilerPass = new FilterPass();
        $compilerPass->process($this->container);
    }

    /**
     * Tests if there at least one filter for manager.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Manager 'ongr_filter_manager.foo_filters' does not have any filters.
     */
    public function testNoFiltersForManager()
    {
        $this->setContainerConfig(
            [
                'ongr_filter_manager.foo_filters' => [],
            ],
            []
        );

        $compilerPass = new FilterPass();
        $compilerPass->process($this->container);
    }

    /**
     * Test FilterPass with correct configuration.
     */
    public function testSetDefinitions()
    {
        $this->setContainerConfig(
            [],
            [
                'custom.filters.foo' => [
                    [
                        'filter_name' => 'foo_filter',
                    ],
                ],
                'custom.filters.bar' => [
                    [
                        'filter_name' => 'bar_filter',
                    ],
                ],
                'ongr_filter_manager.filter.qux_filter' => [
                    [
                        'filter_name' => 'qux_filter',
                    ],
                ],
            ]
        );

        $this->container->expects($this->exactly(2))
            ->method('setAlias')
            ->withConsecutive(
                [$this->equalTo('ongr_filter_manager.filter.foo_filter'), $this->anything()],
                [$this->equalTo('ongr_filter_manager.filter.bar_filter'), $this->anything()]
            );

        $compilerPass = new FilterPass();
        $compilerPass->process($this->container);
    }

    /**
     * Configure container mock.
     *
     * @param array $managers Tagged filter managers.
     * @param array $filters  Tagged filters.
     */
    protected function setContainerConfig($managers, $filters)
    {
        $this->container->expects($this->any())->method('findTaggedServiceIds')->with($this->anything())
            ->will(
                $this->returnCallback(
                    function ($parameter) use ($managers, $filters) {
                        switch ($parameter) {
                            case 'es.filter_manager':
                                return $managers;
                            case 'ongr_filter_manager.filter':
                                return $filters;
                            default:
                                return null;
                        }
                    }
                )
            );
    }
}
