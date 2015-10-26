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
use ONGR\FilterManagerBundle\Test\AbstractFilterManagerResultsTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for range filter.
 */
class RangeTest extends AbstractFilterManagerResultsTest
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
        $managers = $this->getFilterManager();
        $inclusiveManagers = $this->getFilterManagerInclusive();
        // Case #0 range includes everything.
        $out[] = [
            'request' => new Request(['range' => '0;50', 'sort' => '0', 'mode' => null]),
            'ids' => ['1', '2', '3', '4', '5'],
            'assertOrder' => true,
            'managers' => $managers,
        ];

        // Case #1 two elements.
        $out[] = [
            'request' => new Request(['range' => '1;4', 'sort' => '0', 'mode' => null]),
            'ids' => ['2', '3'],
            'assertOrder' => true,
            'managers' => $managers,
        ];

        // Case #2 no elements.
        $out[] = [
            'request' => new Request(['range' => '2;3', 'sort' => '0', 'mode' => null]),
            'ids' => [],
            'assertOrder' => true,
            'managers' => $managers,
        ];

        // Case #3 invalid range specified.
        $out[] = [
            'request' => new Request(['range' => '2', 'sort' => '0', 'mode' => null]),
            'ids' => ['1', '2', '3', '4', '5'],
            'assertOrder' => true,
            'managers' => $managers,
        ];

        // Case #4 no range specified.
        $out[] = [
            new Request(['sort' => '0', 'mode' => null]),
            ['1', '2', '3', '4', '5'],
            true,
            $managers,
        ];

        // Case #5 test with float shouldn't list anything.
        $out[] = [
            'request' => new Request(['range' => '4.3;50', 'sort' => '0', 'mode' => null]),
            'ids' => [],
            'assertOrder' => true,
            'managers' => $managers,
        ];

        // Case #6 test with float should list.
        $out[] = [
            'request' => new Request(['range' => '4.1;50', 'sort' => '0', 'mode' => null]),
            'ids' => ['5'],
            'assertOrder' => true,
            'managers' => $managers,
        ];

        // Case #7 Inclusive filter.
        $out[] = [
            'request' => new Request(['inclusive_range' => '1;2', 'sort' => '0', 'mode' => null]),
            'ids' => ['1', '2'],
            'assertOrder' => true,
            'managers' => $inclusiveManagers,
        ];

        return $out;
    }

    /**
     * Check if view data returned is correct.
     *
     * @param Request $request Http request.
     * @param array $ids Array of document ids to assert.
     * @param bool $assertOrder Set true if order of results lso should be asserted.
     * @param FiltersManager[] $managers Set of filter managers to test.
     *
     * @dataProvider getTestResultsData()
     */
    public function testViewData(Request $request, $ids, $assertOrder = false, $managers = [])
    {
        foreach ($managers as $filter => $filterManager) {
            /** @var RangeAwareViewData $viewData */
            $viewData = $filterManager->execute($request)->getFilters()[$filter];

            $this->assertInstanceOf('ONGR\FilterManagerBundle\Filters\ViewData\RangeAwareViewData', $viewData);
            $this->assertEquals(1, $viewData->getMinBounds());
            $this->assertEquals(4.2, $viewData->getMaxBounds(), '', 0.0001);
        }
    }

    /**
     * Returns filter managers.
     *
     * @return FiltersManager[]
     */
    protected function getFilterManager()
    {
        $managers = [];
        $container = new FiltersContainer();

        $choices = [
            ['label' => 'Stock ASC', 'field' => 'price', 'order' => 'asc', 'default' => false, 'mode' => null],
        ];

        $filter = new Range();
        $filter->setRequestField('range');
        $filter->setField('price');
        $container->set('range', $filter);

        $sort = new Sort();
        $sort->setRequestField('sort');
        $sort->setChoices($choices);
        $container->set('sorting', $sort);

        $managers['range'] = new FiltersManager(
            $container,
            $this->getManager()->getRepository('AcmeTestBundle:Product')
        );

        $managers['bar_range'] = self::createClient()->getContainer()->get('ongr_filter_manager.bar_filters');

        return $managers;
    }

    protected function getFilterManagerInclusive()
    {
        $managers = [];
        $container = new FiltersContainer();

        $choices = [
            ['label' => 'Stock ASC', 'field' => 'price', 'order' => 'asc', 'default' => false, 'mode' => null],
        ];


        $filter = new Range();
        $filter->setRequestField('inclusive_range');
        $filter->setField('price');
        $filter->setInclusive(true);
        $container->set('inclusive_range', $filter);

        $sort = new Sort();
        $sort->setRequestField('sort');
        $sort->setChoices($choices);
        $container->set('sorting', $sort);

        $managers['inclusive_range'] = new FiltersManager(
            $container,
            $this->getManager()->getRepository('AcmeTestBundle:Product')
        );

        $managers['bar_range'] = self::createClient()->getContainer()->get('ongr_filter_manager.bar_filters');

        return $managers;
    }

    /**
     * This method asserts if search request gives expected results.
     *
     * @param Request          $request     Http request.
     * @param array            $ids         Array of document ids to assert.
     * @param bool             $assertOrder Set true if order of results lso should be asserted.
     * @param FiltersManager[] $managers    Set of filter managers to test.
     *
     * @dataProvider getTestResultsData()
     */
    public function testResults(Request $request, $ids, $assertOrder = false, $managers = [])
    {
        foreach ($managers as $filterManager) {
            $actual = array_map(
                [$this, 'fetchDocumentId'],
                iterator_to_array($filterManager->execute($request)->getResult())
            );

            if (!$assertOrder) {
                sort($actual);
                sort($ids);
            }

            $this->assertEquals($ids, $actual);
        }
    }
}
