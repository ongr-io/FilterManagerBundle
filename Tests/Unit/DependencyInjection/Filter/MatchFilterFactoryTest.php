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

use ONGR\FilterManagerBundle\DependencyInjection\Filter\MatchFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MatchFilterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests configure method
     */
    public function testConfigure()
    {
        $container = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $matchFilterFactory = new MatchFilterFactory();
        
        $config = [
            'ongr_filter_manager' => [
                'filters' => [
                    'match' => [
                        'test' => [
                            'request_field' => 'test',
                            'operator' => 'and',
                            'fuzziness' => 0,
                        ]
                    ]
                ]
            ]
        ];
        
        $extension->addFilterFactory($matchFilterFactory);
        $extension->load($config, $container);
        
        $this->assertTrue($container->getDefinition('ongr_filter_manager.filter.test')->hasMethodCall('setOperator'));
        $this->assertTrue($container->getDefinition('ongr_filter_manager.filter.test')->hasMethodCall('setFuzziness'));
    }
}
