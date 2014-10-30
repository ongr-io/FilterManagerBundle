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

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class SearchInfluenceTest extends ElasticsearchTestCase
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
                        'manufacturer' => 'a'
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a'
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b'
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b'
                    ],
                ]
            ]
        ];
    }

    /**
     * @return FiltersManager
     */
    protected function getFiltersManager()
    {
        $container = new FiltersContainer();

        $filter = new SingleTermChoice();
        $filter->setRequestField('c');
        $filter->setField('color');
        $container->set('color', $filter);

        $filter = new SingleTermChoice();
        $filter->setRequestField('m');
        $filter->setField('manufacturer');
        $container->set('manufacturer', $filter);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * @return array
     */
    public function getTestInfluenceData()
    {
        $out = [];

        // case #0 empty request
        $out[] = [
            new Request(),
            'color',
            [
                'red' => 2,
                'blue' => 2,
            ]
        ];

        // case #1 same value when active
        $out[] = [
            new Request(['c' => 'red']),
            'color',
            [
                'red' => 2,
                'blue' => 2,
            ]
        ];

        // case #2 different value when other filter is active
        $out[] = [
            new Request(['m' => 'b']),
            'color',
            [
                'red' => 1,
                'blue' => 1,
            ]
        ];

        return $out;
    }

    /**
     * Test filters influence to each other
     *
     * @dataProvider getTestInfluenceData()
     * @param Request $request
     * @param string $filterName
     * @param array $expected
     */
    public function testInfluence(Request $request, $filterName, $expected)
    {
        /** @var ChoicesAwareViewData $data */
        $data = $this->getFiltersManager()->execute($request)->getFilters()[$filterName];

        $actual = [];
        foreach ($data->getChoices() as $choice) {
            $actual[$choice->getLabel()] = $choice->getCount();
        }

        $this->assertEquals($expected, $actual);
    }
}
