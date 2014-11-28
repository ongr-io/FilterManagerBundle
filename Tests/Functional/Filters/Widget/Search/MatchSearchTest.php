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
                        'title' => 'Awesome'
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Awesomer'
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Awesomo bar'
                    ]
                ]
            ]
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
        $match->setField('title');
        
        $container->set('match', $match);
        
        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * Data provider for filtering
     * 
     * @return array
     */
    public function getTestingData()
    {
        $out = [];
        
        $out[] = [[1], new Request(['q' => 'Awesome'])];
        $out[] = [[3], new Request(['q' => 'bar'])];
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
