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
use ONGR\FilterManagerBundle\Filter\Widget\Search\MatchSearch;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class MatchSearchTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'Foo',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Baz',
                        'description' => 'tuna fish',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Foo bar',
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns filter manager with MatchSearch set.
     *
     * @return FilterManager
     */
    public function getFilerManger()
    {
        $container = new FilterContainer();

        $match = new MatchSearch();
        $match->setRequestField('q');
        $match->setField('title,description');

        $container->set('match', $match);

        return new FilterManager(
            $container,
            $this->getManager()->getRepository('TestBundle:Product'),
            new EventDispatcher()
        );
    }

    /**
     * Data provider for filtering.
     *
     * @return array
     */
    public function getTestingData()
    {
        $out = [];

        // Case #0: simple from title field.
        $out[] = [[1, 3], new Request(['q' => 'Foo'])];
        // Case #1: simple from description field.
        $out[] = [[2], new Request(['q' => 'fish'])];
        // Case #2: empty parameter.
        $out[] = [[1, 2, 3], new Request(['q' => ''])];

        return $out;
    }

    /**
     * Tests if search works.
     *
     * @param array   $expected
     * @param Request $request
     *
     * @dataProvider getTestingData
     */
    public function testFiltering($expected, $request)
    {
        $result = $this->getFilerManger()->handleRequest($request);

        $actual = [];
        foreach ($result->getResult() as $doc) {
            $actual[] = $doc->id;
        }

        sort($actual);

        $this->assertEquals($expected, $actual);
    }
}
