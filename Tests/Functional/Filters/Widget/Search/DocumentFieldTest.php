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

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\Widget\Search\DocumentField;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class DocumentFieldTest extends ElasticsearchTestCase
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
                    ],
                    [
                        '_id' => 2,
                        'categories' => 1,
                    ],
                    [
                        '_id' => 3,
                        'categories' => 2,
                    ],
                    [
                        '_id' => 4,
                        'categories' => [1, 2],
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

        $filter = new DocumentField();
        $filter->setRequestField('document');
        $filter->setField('categories');
        $container->set('category', $filter);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * Data provider for testFiltering()
     *
     * @return array
     */
    public function getTestFilteringData()
    {
        $out = [];

        // Case #0: Filtered results
        $document = $this->getMock('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document->expects($this->once())->method('getId')->willReturn(1);
        $out[] = [
            new Request(['document' => $document]),
            ['2', '4'],
        ];

        // Case #1: Filtered results
        $document = $this->getMock('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document->expects($this->once())->method('getId')->willReturn(2);
        $out[] = [
            new Request(['document' => $document]),
            ['3', '4'],
        ];

        // Case #1: Ignore filter
        $out[] = [
            new Request([]),
            ['1', '2', '3', '4'],
        ];

        return $out;
    }

    /**
     * Test for DocumentField filter
     *
     * @param Request $request
     * @param array   $expectedOrder
     *
     * @dataProvider getTestFilteringData()
     */
    public function testFiltering(Request $request, $expectedOrder)
    {
        $result = $this->getFiltersManager()->execute($request)->getResult();

        $actual = [];
        /** @var DocumentInterface $document */
        foreach ($result as $document) {
            $actual[] = $document->getId();
        }

        sort($actual);

        $this->assertEquals($expectedOrder, $actual);
    }
}
