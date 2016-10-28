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
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
        $this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(
                [
                    'findTaggedServiceIds',
                    'getParameter',
                    'setDefinition',
                ]
            )->getMock();
    }

    public function testPass()
    {
        $this->container->method('getParameter')->willReturnCallback(function ($arg) {
            switch ($arg) {
                case 'ongr_filter_manager.filters':
                    return [
                        'filter.match' => [
                            'type' => 'match',
                            'request_field' => 'acme',
                            'document_field' => 'acme',
                            'tags' => [],
                            'options' => [],
                        ],
                    ];
                case 'ongr_filter_manager.managers':
                    return [
                        'default' => [
                            'filters' => ['match'],
                            'repository' => 'foo'
                        ]
                    ];
                default:
                    return [];
            }
        });

        $this->container->expects($this->once())->method('findTaggedServiceIds')->willReturn(
            [
                'filter' => [
                    [
                        'type' => 'match'
                    ]
                ],
            ]
        );

        $filterPass = new FilterPass();
        $filterPass->process($this->container);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testPassWhenThereIsNo()
    {
        $this->container->expects($this->once())->method('findTaggedServiceIds')->willReturn(
            [
                'filter' => [
                    [
                        'no_type' => 'match'
                    ]
                ],
            ]
        );

        $filterPass = new FilterPass();
        $filterPass->process($this->container);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testPassWhenThereIsNoMathingFilter()
    {
        $this->container->method('getParameter')->willReturn([
            'filter.match' => [
                'type' => 'match',
                'request_field' => 'acme',
                'document_field' => 'acme',
                'tags' => [],
                'options' => [],
            ],
        ]);
        $this->container->expects($this->once())->method('findTaggedServiceIds')->willReturn(
            [
                'filter' => [
                    [
                        'type' => 'choice'
                    ]
                ],
            ]
        );

        $filterPass = new FilterPass();
        $filterPass->process($this->container);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testPassWhenFilterNameIsTheSameAsTYpe()
    {
        $this->container->method('getParameter')->willReturn([
            'match' => [
                'type' => 'match',
                'request_field' => 'acme',
                'document_field' => 'acme',
                'tags' => [],
                'options' => [],
            ],
        ]);
        $this->container->expects($this->once())->method('findTaggedServiceIds')->willReturn(
            [
                'filter.match' => [
                    [
                        'type' => 'match'
                    ]
                ],
            ]
        );

        $filterPass = new FilterPass();
        $filterPass->process($this->container);
    }
}
