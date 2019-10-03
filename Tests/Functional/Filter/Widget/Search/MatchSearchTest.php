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

use App\Document\Product;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\HttpFoundation\Request;

class MatchSearchTest extends AbstractElasticsearchTestCase
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
                    'title' => 'Foo',
                    'description' => 'fish',
                    'attributes' => [
                        [
                            'name' => 'acme'
                        ],
                        [
                            'name' => 'foo'
                        ],
                    ],
                ],
                [
                    '_id' => 2,
                    'title' => 'Baz',
                    'description' => 'tuna fish',
                    'attributes' => [
                        [
                            'name' => 'foo'
                        ],
                        [
                            'name' => 'bar'
                        ],
                    ],
                ],
                [
                    '_id' => 3,
                    'title' => 'Foo bar',
                    'description' => 'bar acme acme',
                    'attributes' => [
                        [
                            'name' => 'acme'
                        ]
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
        $out[] = [
            [2, 1, 3],
        ];

        // Case #1
        $out[] = [
            [1, 3],
            ['qn' => 'acme'],
        ];

        // Case #2
        $out[] = [
            [3],
            ['qm' => 'bar'],
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
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('search'));
        $result = $manager->handleRequest(new Request($query))->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals($expectedChoices, $actual);
    }

    protected function setUp()
    {
        $this->getIndex(Product::class);
    }
}
