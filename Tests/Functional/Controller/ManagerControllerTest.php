<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Controller;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

class ManagerControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'Foo product',
                        'color' => 'red',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Foo cool product',
                        'color' => 'red',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Another cool product',
                        'color' => 'red',
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests if manager controller works as expected.
     */
    public function testManagerAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/list');

        $this->assertTrue($client->getResponse()->isOk(), 'Client should return 200 code.');
        $this->assertEquals(3, $crawler->filter('ul > li')->count(), 'There should be generated 3 li elements.');
    }
}
