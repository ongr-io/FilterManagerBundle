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
use Symfony\Component\HttpFoundation\Request;

class BoostedMatchTest extends AbstractElasticsearchTestCase
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
                        'description' => 'Foo',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Bar',
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
        $match->setField('title^0.5,description^2');

        $container->set('match', $match);

        return new FilterManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
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
        $out[] = [[3], new Request(['q' => 'Bar'])];

        // Case #1: Test if word in description is boosted more
        $out[] = [[2, 1], new Request(['q' => 'Foo'])];

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

        $this->assertEquals($expected, $actual);
    }
}
