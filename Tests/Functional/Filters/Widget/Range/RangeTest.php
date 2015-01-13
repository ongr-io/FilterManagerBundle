<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filters\Range;

use ONGR\FilterManagerBundle\Filters\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Filters\Widget\Range\Range;
use ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use ONGR\FilterManagerBundle\Test\FilterManagerResultsTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for range filter.
 */
class RangeTest extends FilterManagerResultsTest
{
    /**
     * @return array
     */
    public function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'color' => 'red',
                        'manufacturer' => 'a',
                        'price' => 1,
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                        'price' => 2,
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                        'price' => 3,
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'price' => 4,
                    ],
                    [
                        '_id' => 5,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'price' => 4.2,
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0 range includes everything.
        $out[] = [new Request(['range' => '0;50', 'sort' => '0']), ['1', '2', '3', '4', '5'], true];

        // Case #1 two elements.
        $out[] = [new Request(['range' => '1;4', 'sort' => '0']), ['2', '3'], true];

        // Case #2 no elements.
        $out[] = [new Request(['range' => '2;3', 'sort' => '0']), [], true];

        // Case #3 invalid range specified.
        $out[] = [new Request(['range' => '2', 'sort' => '0']), ['1', '2', '3', '4', '5'], true];

        // Case #4 no range specified.
        $out[] = [new Request(['sort' => '0']), ['1', '2', '3', '4', '5'], true];

        // Case #5 test with float shouldn't list anything.
        $out[] = [new Request(['range' => '4.3;50', 'sort' => '0']), [], true];

        // Case #6 test with float should list.
        $out[] = [new Request(['range' => '4.1;50', 'sort' => '0']), ['5'], true];

        return $out;
    }

    /**
     * Check if view data returned is correct.
     *
     * @param Request $request Http request.
     *
     * @dataProvider getTestResultsData()
     */
    public function testViewData(Request $request)
    {
        /** @var RangeAwareViewData $viewData */
        $viewData = $this->getFilterManager()->execute($request)->getFilters()['range'];

        $this->assertInstanceOf('ONGR\FilterManagerBundle\Filters\ViewData\RangeAwareViewData', $viewData);
        $this->assertEquals(1, $viewData->getMinBounds());
        $this->assertEquals(4.2, $viewData->getMaxBounds(), '', 0.0001);
    }

    /**
     * Returns filter manager.
     *
     * @return FiltersManager
     */
    protected function getFilterManager()
    {
        $container = new FiltersContainer();

        $choices = [
            ['label' => 'Stock ASC', 'field' => 'price', 'order' => 'asc', 'default' => false],
        ];

        $filter = new Range();
        $filter->setRequestField('range');
        $filter->setField('price');
        $container->set('range', $filter);

        $sort = new Sort();
        $sort->setRequestField('sort');
        $sort->setChoices($choices);
        $container->set('sorting', $sort);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }
}
