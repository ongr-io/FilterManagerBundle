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
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\HttpFoundation\Request;

class FuzzySearchTest extends AbstractElasticsearchTestCase
{
    /**
     * @return array
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'Foo',
                        'color' => 'red',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Baz',
                        'description' => 'tuna fish',
                        'color' => 'blue',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Foo bar',
                        'color' => 'yellow',
                    ],
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
            [3, 1, 2],
        ];

        // Case #1
        $out[] = [
            [1,3],
            ['f1' => 'oo']
        ];

        // Case #2
        $out[] = [
            [3],
            ['f2' => 'ar']
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
}
