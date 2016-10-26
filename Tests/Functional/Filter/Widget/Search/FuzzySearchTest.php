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
use ONGR\FilterManagerBundle\Filter\Widget\Search\FuzzySearch;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
     * Data provider for filtering.
     *
     * @return array
     */
    public function getTestingData()
    {
        $out = [];

        // Case #0: Fuzziness parameter.
        $out[] = [[2], new Request(['q' => 'lue']), 1];
        // Case #1: Added prefix_length parameter - search term doesn't begin with 'b' so 'blue' doesn't match.
        $out[] = [[], new Request(['q' => 'lue']), 1, 1];

        return $out;
    }

    /**
     * Tests if search works.
     *
     * @param array            $expected
     * @param Request          $request
     * @param string|int|float $fuzziness
     * @param int              $prefixLength
     * @param int              $maxExpansions
     *
     * @dataProvider getTestingData
     */
    public function testFiltering($expected, $request, $fuzziness = null, $prefixLength = null, $maxExpansions = null)
    {
        $container = new FilterContainer();

        $fuzzy = new FuzzySearch();
        $fuzzy->setRequestField('q');
        $fuzzy->setDocumentField('color');
        $fuzzy->setFuzziness($fuzziness);
        $fuzzy->setPrefixLength($prefixLength);
        $fuzzy->setMaxExpansions($maxExpansions);

        $container->set('fuzzy', $fuzzy);

        $fmb = new FilterManager(
            $container,
            $this->getManager()->getRepository('TestBundle:Product'),
            new EventDispatcher()
        );
        $result = $fmb->handleRequest($request);

        $actual = [];
        foreach ($result->getResult() as $doc) {
            $actual[] = $doc->id;
        }

        sort($actual);

        $this->assertEquals($expected, $actual);
    }
}
