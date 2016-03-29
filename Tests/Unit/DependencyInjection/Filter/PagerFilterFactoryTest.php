<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Unit\Unit\DependencyInjection\Filter;

use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\PagerFilterFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PagerFilterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests configure method
     */
    public function testConfigure()
    {
        $container = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();
        $pagerFactory = new PagerFilterFactory();
        $extension->addFilterFactory($pagerFactory);
        $config = ['ongr_filter_manager'=>[
            'filters' => [
                'pager' => [
                    'pager' => [
                        'relations' => [],
                        'request_field' => 'page',
                        'count_per_page' => 10,
                        'max_pages' => 8
                    ]
                ]
            ]
        ]];
        $extension->load($config, $container);
        $this->assertTrue(
            $container->getDefinition('ongr_filter_manager.filter.pager')
                ->hasMethodCall('setCountPerPage')
        );
        $this->assertTrue(
            $container->getDefinition('ongr_filter_manager.filter.pager')
                ->hasMethodCall('setMaxPages')
        );
    }
}
