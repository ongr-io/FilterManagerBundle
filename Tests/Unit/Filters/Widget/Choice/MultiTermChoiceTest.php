<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filters\Widget\Choice;

use ONGR\FilterManagerBundle\Filters\Widget\Choice\MultiTermChoice;

class MultiTermChoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests modifySearch method.
     */
    public function testModifySearch()
    {
        $mockFilterState = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\FilterState')->getMock();
        $mockFilterState->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(true));

        $mockSearch = $this->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')->getMock();
        $mockSearch->expects($this->once())
            ->method('addPostFilter')
            ->with($this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Filter\TermsFilter'));

        $mtc = new MultiTermChoice();
        $mtc->modifySearch($mockSearch, $mockFilterState);
    }

    /**
     * Data provider for testGetViewData().
     *
     * @return array
     */
    public function getTestGetViewDataData()
    {
        return [
            // Case #1, no URL parameters in ViewData object.
            [
                $this->getDocumentIteratorMock(
                    [
                        'testName' => [
                            $this->getValueAggregationMock('country', 10),
                            $this->getValueAggregationMock('color', 3),
                            $this->getValueAggregationMock('manufacturer', 14),
                        ],
                    ]
                ),
                $this->getFilterStateMock(true, ['color']),
                [],
            ],
            // Case #2, some URL parameters in ViewData object.
            [
                $this->getDocumentIteratorMock(
                    [
                        'testName' => [
                            $this->getValueAggregationMock('country', 10),
                            $this->getValueAggregationMock('color', 3),
                            $this->getValueAggregationMock('manufacturer', 14),
                        ],
                    ]
                ),
                $this->getFilterStateMock(true, ['color']),
                [
                    'choice' => ['', ''],
                    'param' => [''],
                ],
            ],
        ];
    }

    /**
     * Tests getViewData method.
     *
     * @param DocumentIterator $documentIterator      Document.
     * @param FilterState      $filterState           Filter state of ViewData object..
     * @param array            $viewDataUrlParameters URL parameters of ViewData object.
     *
     * @dataProvider getTestGetViewDataData
     */
    public function testGetViewData($documentIterator, $filterState, $viewDataUrlParameters)
    {
        $mtc = new MultiTermChoice();
        $mtc->setRequestField('choice');
        $mtc->setSortType(['type' => '', 'order' => '', 'priorities' => ['country']]);

        $viewData = $mtc->createViewData();
        $viewData->setName('testName');
        $viewData->setState($filterState);
        $viewData->setUrlParameters($viewDataUrlParameters);

        $mtc->getViewData($documentIterator, $viewData);
    }

    /**
     * Returns a mock object of ValueAggregation class.
     *
     * @param string $key      Key name.
     * @param int    $docCount Number of documents.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getValueAggregationMock($key, $docCount)
    {
        $mock = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\Aggregation\ValueAggregation')
            ->setConstructorArgs([[]])
            ->getMock();
        $mock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(['key' => $key, 'doc_count' => $docCount]));

        return $mock;
    }

    /**
     * Returns a mock object of FilterState class.
     *
     * @param bool   $isActive Is filter active.
     * @param string $getValue Key name.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilterStateMock($isActive, $getValue = '')
    {
        $mock = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\FilterState')->getMock();
        $mock->expects($this->exactly(3))
            ->method('isActive')
            ->will($this->returnValue($isActive));
        if ($isActive) {
            $mock->expects($this->exactly(3))
                ->method('getValue')
                ->will($this->returnValue($getValue));
        }

        return $mock;
    }

    /**
     * Returns a mock object of DocumentIterator class.
     *
     * @param AggregationIterator|array $getAggregations Aggregations from document.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDocumentIteratorMock($getAggregations)
    {
        $mock = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->setConstructorArgs([[], [], []])
            ->getMock();
        $mock->expects($this->once())
            ->method('getAggregations')
            ->will($this->returnValue($getAggregations));

        return $mock;
    }

    /**
     * Return a mock object of AggregationIterator class.
     *
     * @param ValueAggegation[]|null $find Return value of find method.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAggregationIteratorMock($find)
    {
        $mock = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\Aggregation\AggregationIterator')
            ->setConstructorArgs([[]])
            ->getMock();
        $mock->expects($this->once())
            ->method('find')
            ->will($this->returnValue($find));

        return $mock;
    }
}
