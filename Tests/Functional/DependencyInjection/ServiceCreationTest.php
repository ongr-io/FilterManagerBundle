<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\DependencyInjection;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager;
use ONGR\FilterManagerBundle\Filters\Widget\Range\Range;

class ServiceCreationTest extends ElasticsearchTestCase
{
    /**
     * Data provider for testing service creation.
     *
     * @return array
     */
    public function getTestServicesData()
    {
        return [
            [
                'ongr_filter_manager.filter.phrase',
                'ONGR\FilterManagerBundle\Filters\Widget\Search\MatchSearch',
            ],
            [
                'ongr_filter_manager.filter.pager',
                'ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager',
                [
                    'getCountPerPage' => 12,
                    'getRequestField' => 'page',
                ],
            ],
            [
                'ongr_filter_manager.filter.range',
                'ONGR\FilterManagerBundle\Filters\Widget\Range\Range',
                [
                    'getField' => 'price',
                    'getRequestField' => 'range',
                ],
            ],
            [
                'ongr_filter_manager.filter.choice',
                'ONGR\FilterManagerBundle\Filters\Widget\Choice\MultiTermChoice',
                [
                    'getField' => 'choice',
                    'getRequestField' => 'choice',
                ],
            ],
            [
                'ongr_filter_manager.foo_filters',
                'ONGR\FilterManagerBundle\Search\FiltersManager',
            ],
        ];
    }

    /**
     * Test for filter services registration.
     *
     * @param string $id
     * @param string $instance
     * @param array  $params
     *
     * @dataProvider getTestServicesData()
     */
    public function testServices($id, $instance, $params = [])
    {
        $container = self::createClient()->getContainer();

        $this->assertTrue($container->has($id));
        $service = $container->get($id);
        $this->assertInstanceOf($instance, $service);
        
        foreach ($params as $method => $value) {
            $this->assertEquals($value, $service->$method());
        }
    }
}
