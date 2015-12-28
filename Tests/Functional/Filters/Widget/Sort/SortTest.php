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
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class SortTest extends AbstractElasticsearchTestCase
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
                        // Average = 3, sum = 15.
                        'items' => [1, 2, 3, 4, 5],
                        'words' => ['one', 'two', 'three', 'alfa', 'beta'],
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                        'stock' => 6,
                        // Average = 7.2, sum = 36.
                        'items' => [2, 12, 3, 14, 5],
                        'words' => ['eta', 'geta', 'zeta', 'beta', 'deta'],
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                        'stock' => 2,
                        // Average = 3.2, sum = 16.
                        'items' => [5, 4, 3, -12, 16],
                        'words' => ['vienas', 'du', 'trys', 'keturi', 'penki'],
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                        'stock' => 7,
                        // Average = 6, sum = 30.
                        'items' => [1, -2, 30, -4, 5],
                        'words' => ['eins', 'zwei', 'drei', 'fier', 'funf'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return FiltersManager
     */
    protected function getFiltersManager()
    {
        $container = new FiltersContainer();

        $choices = [
            ['label' => 'Stock ASC', 'field' => 'stock', 'order' => 'asc', 'default' => false, 'mode' => null],
            ['label' => 'Stock DESC', 'field' => 'stock', 'order' => 'desc', 'default' => true, 'mode' => null],
            [
                'label' => 'Stock Keyed',
                'field' => 'stock',
                'order' => 'desc',
                'default' => false,
                'key' => 'foo',
                'mode' => null,
            ],
            ['label' => 'Items ASC', 'field' => 'items', 'order' => 'asc', 'default' => false, 'mode' => 'min'],
            ['label' => 'Items ASC', 'field' => 'items', 'order' => 'desc', 'default' => false, 'mode' => 'max'],
            ['label' => 'Items ASC', 'field' => 'items', 'order' => 'asc', 'default' => false, 'mode' => 'avg'],
            ['label' => 'Items ASC', 'field' => 'items', 'order' => 'asc', 'default' => false, 'mode' => 'sum'],
            ['label' => 'Items ASC', 'field' => 'words', 'order' => 'asc', 'default' => false, 'mode' => 'min'],
            ['label' => 'Items ASC', 'field' => 'words', 'order' => 'asc', 'default' => false, 'mode' => 'max'],
            ['label' => 'Items ASC', 'field' => 'words', 'order' => 'asc', 'default' => false, 'mode' => 'avg'],
            ['label' => 'Items ASC', 'field' => 'words', 'order' => 'asc', 'default' => false, 'mode' => 'sum'],
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

        // Case #3: empty sort, should fallback to default.
        $out[] = [
            new Request(['sort' => '']),
            ['4', '2', '1', '3'],
        ];

        // Case #4: mode set to min on integer array.
        $out[] = [
            new Request(['sort' => 3]),
            ['3', '4', '1', '2'],
        ];

        // Case #5: mode set to max on integer array.
        $out[] = [
            new Request(['sort' => 4]),
            ['4', '3', '2', '1'],
        ];

        // Case #6: mode set to avg on integer array.
        $out[] = [
            new Request(['sort' => 5]),
            ['1', '3', '4', '2'],
        ];

        // Case #7: mode set to sum on integer array.
        $out[] = [
            new Request(['sort' => 6]),
            ['1', '3', '4', '2'],
        ];

        // Case #8: mode set to min on string array.
        $out[] = [
            new Request(['sort' => 7]),
            ['1', '2', '4', '3'],
        ];

        // Case #9: mode set to max on string array.
        $out[] = [
            new Request(['sort' => 8]),
            ['1', '3', '2', '4'],
        ];

        // Case #10: mode set to avg on string array changes to mode min.
        $out[] = [
            new Request(['sort' => 9]),
            ['1', '2', '4', '3'],
        ];

        // Case #11: mode set to sum on string array changes to mode min.
        $out[] = [
            new Request(['sort' => 10]),
            ['1', '2', '4', '3'],
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
        $result = $this->getFiltersManager()->handleRequest($request)->getResult();

        $actual = [];
        /** @var DocumentInterface $document */
        foreach ($result as $document) {
            $actual[] = $document->getId();
        }

        $this->assertSame($expectedOrder, $actual);
    }
}
