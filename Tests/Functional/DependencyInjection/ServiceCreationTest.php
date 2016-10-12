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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceCreationTest extends WebTestCase
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
                'ONGR\FilterManagerBundle\Filter\Widget\Search\MatchSearch',
                [
                    'getRequestField' => 'q',
                    'getDocumentField' => 'title',
                ],
            ],
            [
                'ongr_filter_manager.filter.page',
                'ONGR\FilterManagerBundle\Filter\Widget\Pager\Pager',
                [
                    'getCountPerPage' => 12,
                    'getRequestField' => 'page',
                    'getDocumentField' => null,
                ],
            ],
            [
                'ongr_filter_manager.filter.single_choice',
                'ONGR\FilterManagerBundle\Filter\Widget\Choice\SingleTermChoice',
                [
                    'getRequestField' => 'color',
                    'getDocumentField' => 'color',
                ],
            ],
            [
                'ongr_filter_manager.filter.zero_choices',
                'ONGR\FilterManagerBundle\Filter\Widget\Choice\SingleTermChoice',
                [
                    'getRequestField' => 'zero',
                    'getDocumentField' => 'sku',
                ],
            ],
            [
                'ongr_filter_manager.filter.price_range',
                'ONGR\FilterManagerBundle\Filter\Widget\Range\Range',
                [
                    'getDocumentField' => 'price',
                    'getRequestField' => 'range',
                    'getTags' => ['badged', 'permanent'],
                ],
            ],
            [
                'ongr_filter_manager.filter.inclusive_range',
                'ONGR\FilterManagerBundle\Filter\Widget\Range\Range',
                [
                    'getDocumentField' => 'price',
                    'getRequestField' => 'inclusive_range',
                    'getTags' => [],
                ],
            ],
            [
                'ongr_filter_manager.filter.date',
                'ONGR\FilterManagerBundle\Filter\Widget\Range\DateRange',
                [
                    'getDocumentField' => 'date',
                    'getRequestField' => 'date_range',
                    'getTags' => [],
                ],
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
        $client = self::createClient();
        $container = $client->getContainer();

        $this->assertTrue($container->has($id));
        $service = $container->get($id);
        $this->assertInstanceOf($instance, $service);
        
        foreach ($params as $method => $value) {
            $this->assertEquals($value, $service->$method());
        }
    }
}
