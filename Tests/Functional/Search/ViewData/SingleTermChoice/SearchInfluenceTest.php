<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Search\ViewData\SingleTermChoice;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Choice\SingleTermChoice;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

class SearchInfluenceTest extends AbstractElasticsearchTestCase
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
                        'color' => 'red',
                        'manufacturer' => 'a',
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return FilterManager
     */
    protected function getFilterManager()
    {
        $container = new FilterContainer();

        $filter = new SingleTermChoice();
        $filter->setRequestField('c');
        $filter->setDocumentField('color');
        $filter->addOption('sort_type', '_term');
        $filter->addOption('sort_order', 'asc');
        $container->set('color', $filter);

        $filter = new SingleTermChoice();
        $filter->setRequestField('m');
        $filter->setDocumentField('manufacturer');
        $filter->addOption('sort_type', '_term');
        $filter->addOption('sort_order', 'asc');
        $container->set('manufacturer', $filter);

        return new FilterManager(
            $container,
            $this->getManager()->getRepository('TestBundle:Product'),
            new EventDispatcher()
        );
    }

    /**
     * @return array
     */
    public function getTestInfluenceData()
    {
        $out = [];

        // Case #0 empty request.
        $out[] = [
            new Request(),
            'color',
            [
                'red' => 2,
                'blue' => 2,
            ],
        ];

        // Case #1 same value when active.
        $out[] = [
            new Request(['c' => 'red']),
            'color',
            [
                'red' => 2,
                'blue' => 2,
            ],
        ];

        // Case #2 different value when other filter is active.
        $out[] = [
            new Request(['m' => 'b']),
            'color',
            [
                'red' => 1,
                'blue' => 1,
            ],
        ];

        return $out;
    }

    /**
     * Test filters influence to each other.
     *
     * @param Request $request
     * @param string  $filterName
     * @param array   $expected
     *
     * @dataProvider getTestInfluenceData()
     */
    public function testInfluence(Request $request, $filterName, $expected)
    {
        /** @var ChoicesAwareViewData $data */
        $data = $this->getFilterManager()->handleRequest($request)->getFilters()[$filterName];

        $actual = [];
        foreach ($data->getChoices() as $choice) {
            $actual[$choice->getLabel()] = $choice->getCount();
        }

        $this->assertEquals($expected, $actual);
    }
}
