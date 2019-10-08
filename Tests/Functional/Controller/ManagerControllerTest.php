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

use App\Document\Product;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

class ManagerControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            Product::class => [
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

    /**
     * Test JSON action.
     */
    public function testJsonAction()
    {
        $client = static::createClient();
        $client->request('GET', '/list.json');

        $response = $client->getResponse();
        $this->assertTrue($response->isOk(), 'Client should return 200 code.');
        $this->assertJson($response->getContent());

        $data = json_decode($response->getContent(), true);

        // Check that json is not "pretty"
        $this->assertTrue(substr_count($response->getContent(), PHP_EOL) <= 1);

        $this->assertCount(3, $data['documents']);
    }

    /**
     * Test JSON action with pretty argument.
     */
    public function testPrettyJsonAction()
    {
        $client = static::createClient();
        $client->request('GET', '/list.json', ['pretty' => true]);

        $response = $client->getResponse();
        $this->assertJson($response->getContent());

        $this->assertTrue(substr_count($response->getContent(), PHP_EOL) > 1);
    }

    protected function setUp()
    {
        $this->getIndex(Product::class);
    }
}
