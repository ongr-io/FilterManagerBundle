<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Search;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use App\Document\Product;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\HttpFoundation\Request;

class DocumentValueTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getDataArray()
    {
        return [
            Product::class => [
                [
                    '_id' => 1,
                    'category' => [
                        'jeans',
                        'shirts',
                    ]
                ],
                [
                    '_id' => 2,
                    'category' => [
                        'jeans',
                    ]
                ],
                [
                    '_id' => 3,
                    'category' => [
                        'shirts',
                    ]
                ],
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0
        $document = new Product();
        $document->category = 'jeans';
        $out[] = [
            [1,2],
            ['document' => $document],
        ];

        // Case #1
        $document = new Product();
        $document->category = 'shirts';
        $out[] = [
            [3, 1],
            ['document' => $document],
        ];

        return $out;
    }

    /**
     * Check if choices are filtered and sorted as expected.
     *
     * @param array $expectedChoices
     * @param array $query
     *
     * @dataProvider getTestResultsData()
     */
    public function testFilter($expectedChoices, $query = [])
    {
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('document_value'));
        $result = $manager->handleRequest(new Request($query))->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals(sort($expectedChoices), sort($actual));
    }

    protected function setUp()
    {
        $this->getIndex(Product::class);
    }
}
