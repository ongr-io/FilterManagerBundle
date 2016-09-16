<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\DependencyInjection\Filter;

use ONGR\FilterManagerBundle\DependencyInjection\Filter\DynamicAggregateFactory;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DynamicAggregateFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests configure method
     */
    public function testConfigure()
    {
        $container = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $matchFilterFactory = new DynamicAggregateFactory();

        $config = [
            'ongr_filter_manager' => [
                'filters' => [
                    'dynamic_aggregate' => [
                        'test' => [
                            'request_field' => 'test',
                            'field' => 'foo',
                            'name_field' => 'bar',
                            'sort' => ['priorities' => ['foo']]
                        ]
                    ]
                ]
            ]
        ];

        $extension->addFilterFactory($matchFilterFactory);
        $extension->load($config, $container);

        $this->assertTrue($container->getDefinition('ongr_filter_manager.filter.test')->hasMethodCall('setField'));
        $this->assertTrue($container->getDefinition('ongr_filter_manager.filter.test')->hasMethodCall('setNameField'));
        $this->assertTrue($container->getDefinition('ongr_filter_manager.filter.test')->hasMethodCall('setSortType'));
    }
}
