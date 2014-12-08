<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filters\Widget\Sort;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class SortTest extends ElasticsearchTestCase
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
                        'stock' => 5,
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                        'stock' => 6,
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                        'stock' => 2,
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'stock' => 7,
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

        $choices = [
            ['label' => 'Stock ASC', 'field' => 'stock', 'order' => 'asc', 'default' => false],
            ['label' => 'Stock DESC', 'field' => 'stock', 'order' => 'desc', 'default' => false],
            ['label' => 'Stock Keyed', 'field' => 'stock', 'order' => 'desc', 'default' => false, 'key' => 'foo'],
        ];

        $filter = new Sort();
        $filter->setRequestField('sort');
        $filter->setChoices($choices);
        $container->set('sorting', $filter);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * Data provider for testSorting().
     *
     * @return array
     */
    public function getTestSortingData()
    {
        $out = [];

        // Case #0: ascending sorting.
        $out[] = [
            new Request(['sort' => 0]),
            ['3', '1', '2', '4'],
        ];

        // Case #1: descending sorting.
        $out[] = [
            new Request(['sort' => 1]),
            ['4', '2', '1', '3'],
        ];

        // Case #2: using keyed parameters.
        $out[] = [
            new Request(['sort' => 'foo']),
            ['4', '2', '1', '3'],
        ];

        return $out;
    }

    /**
     * Test sorting filter.
     *
     * @param Request $request
     * @param array   $expectedOrder
     *
     * @dataProvider getTestSortingData()
     */
    public function testSorting(Request $request, $expectedOrder)
    {
        $result = $this->getFiltersManager()->execute($request)->getResult();

        $actual = [];
        /** @var DocumentInterface $document */
        foreach ($result as $document) {
            $actual[] = $document->getId();
        }

        $this->assertSame($expectedOrder, $actual);
    }
}
