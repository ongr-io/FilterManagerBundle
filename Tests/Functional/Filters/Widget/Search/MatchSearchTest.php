<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filters\Widget\Search;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\Widget\Search\MatchSearch;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class MatchSearchTest extends ElasticsearchTestCase
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
     * @return FiltersManager
     */
    public function getFilerManger()
    {
        $container = new FiltersContainer();

        $match = new MatchSearch();
        $match->setRequestField('q');
        $match->setField('title,description');

        $container->set('match', $match);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
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
        $result = $this->getFilerManger()->execute($request);

        $actual = [];
        foreach ($result->getResult() as $doc) {
            $actual[] = $doc->getId();
        }

        sort($actual);

        $this->assertEquals($expected, $actual);
    }
}
