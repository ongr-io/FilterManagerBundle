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

use ONGR\FilterManagerBundle\Filter\Widget\Search\DocumentField;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use ONGR\FilterManagerBundle\Test\AbstractFilterManagerResultsTest;
use Symfony\Component\HttpFoundation\Request;

class DocumentFieldTest extends AbstractFilterManagerResultsTest
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
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterManager()
    {
        $container = new FilterContainer();

        $filter = new DocumentField();
        $filter->setRequestField('document');
        $filter->setField('categories');
        $container->set('category', $filter);

        return new FilterManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * {@inheritdoc}
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0: Filtered results.
        $document = $this->getMock('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document->expects($this->once())->method('getId')->willReturn(1);
        $out[] = [
            new Request(['document' => $document]),
            ['2', '4'],
        ];

        // Case #1: Filtered results.
        $document = $this->getMock('ONGR\ElasticsearchBundle\Document\DocumentInterface');
        $document->expects($this->once())->method('getId')->willReturn(2);
        $out[] = [
            new Request(['document' => $document]),
            ['3', '4'],
        ];

        // Case #1: Ignore filter.
        $out[] = [
            new Request([]),
            ['1', '2', '3', '4'],
        ];

        return $out;
    }
}
