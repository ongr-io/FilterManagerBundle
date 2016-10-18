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
     * Check if expected method calls are added to the filter definition.
     */
    public function testMethodCalls()
    {
        $config = [
            'ongr_filter_manager' => [
                'managers' => [
                    'test' => [
                        'repository' => 'test',
                        'filters' => [
                            'choice',
                            'pager',
                        ],
                    ],
                ],
                'filters' => [
                    'choice' => [
                        'request_field' => 'acme',
                        'document_field' => 'acme',
                        'tags' => [
                            'acme',
                            'foo'
                        ]
                    ],
                    'pager' => [
                        'type' => 'pager',
                        'request_field' => 'page',
                        'document_field' => null,
                        'options' => [
                            'count_per_page' => 10,
                            'max_pages' => 8,
                        ],
                    ],
                ],
            ],
        ];

        $containerBuilder = new ContainerBuilder();
        $extension = new ONGRFilterManagerExtension();

        $extension->load($config, $containerBuilder);

        $this->assertTrue($containerBuilder->hasParameter('ongr_filter_manager.filters'));
        $this->assertTrue($containerBuilder->hasParameter('ongr_filter_manager.managers'));
        $this->assertEquals(2, count($containerBuilder->getParameter('ongr_filter_manager.filters')));
    }

    public function testFilterNameGetter()
    {
        $this->assertEquals(
            ONGRFilterManagerExtension::PREFIX.'.filter.acme',
            ONGRFilterManagerExtension::getFilterId('acme')
        );
    }

    public function testFilterManagerNameGetter()
    {
        $this->assertEquals(
            ONGRFilterManagerExtension::PREFIX.'.manager.acme',
            ONGRFilterManagerExtension::getFilterManagerId('acme')
        );
    }
}
