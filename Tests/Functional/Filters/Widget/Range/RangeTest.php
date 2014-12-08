<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\PagerBundle\Tests\Functional\Filters\Range;

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
                        'stock' => 1,
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                        'stock' => 2,
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                        'stock' => 3,
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'stock' => 4,
                    ],
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0 range includes everything.
        $out[] = [new Request(['range' => '0;50', 'sort' => '0']), ['1', '2', '3', '4'], true];

        // Case #1 two elements.
        $out[] = [new Request(['range' => '1;4', 'sort' => '0']), ['2', '3'], true];

        // Case #2 no elements.
        $out[] = [new Request(['range' => '2;3', 'sort' => '0']), [], true];

        return $out;
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
            ['label' => 'Stock ASC', 'field' => 'stock', 'order' => 'asc', 'default' => false],
        ];

        $filter = new Range();
        $filter->setRequestField('range');
        $filter->setField('stock');
        $container->set('range', $filter);

        $sort = new Sort();
        $sort->setRequestField('sort');
        $sort->setChoices($choices);
        $container->set('sorting', $sort);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }
}
