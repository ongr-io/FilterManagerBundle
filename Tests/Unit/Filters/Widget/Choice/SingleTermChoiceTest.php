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

use ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice;

class SingleTermChoiceTest extends \PHPUnit_Framework_TestCase
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
            ->with($this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Filter\TermFilter'));

        $stc = new SingleTermChoice();
        $stc->modifySearch($mockSearch, $mockFilterState);
    }

    /**
     * Tests preProcessSearch method.
     */
    public function testPreProcessSearch()
    {
        $mockFilterState = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\FilterState')->getMock();
        $mockFilterState->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('name'));

        $mockSearch = $this->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')->getMock();
        $mockSearch->expects($this->at(0))
            ->method('addAggregation')
            ->with($this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Aggregation\FilterAggregation'));
        $mockSearch->expects($this->at(1))
            ->method('addAggregation')
            ->with($this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Aggregation\TermsAggregation'));
        $mockSearch->expects($this->exactly(2))
            ->method('addAggregation');

        $mockRelatedSearch = $this->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')->getMock();
        $mockRelatedSearch->expects($this->exactly(2))
            ->method('getPostFilters')
            ->will(
                $this->returnValue(
                    $this
                        ->getMockBuilder('ONGR\ElasticsearchBundle\DSL\BuilderInterface')
                        ->getMock()
                )
            );

        $stc = new SingleTermChoice();
        $stc->preProcessSearch($mockSearch, $mockRelatedSearch, $mockFilterState);

        $mockRelatedSearch = $this->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')->getMock();
        $mockRelatedSearch->expects($this->once())
            ->method('getPostFilters')
            ->will($this->returnValue(null));

        $stc->setSortType(['type' => '', 'order' => '']);
        $stc->preProcessSearch($mockSearch, $mockRelatedSearch, $mockFilterState);
    }

    /**
     * Tests createViewData method.
     */
    public function testCreateViewData()
    {
        $stc = new SingleTermChoice();
        $this->assertInstanceOf(
            'ONGR\FilterManagerBundle\Filters\ViewData\ChoicesAwareViewData',
            $stc->createViewData()
        );
    }

    /**
     * Data provider for testGetViewData().
     *
     * @return array
     */
    public function getTestGetViewDataData()
    {
        return [
            // Case #1, inactive filter state, prioritized choice, document returns ValueAggregation[].
            [
                $this->getDocumentIteratorMock(
                    [
                        'testName' => [ $this->getValueAggregationMock('country', 10) ],
                    ]
                ),
                $this->getFilterStateMock(false),
            ],
            // Case #2, active filter state, non-prioritized choice, document returns non-empty AggregationIterator.
            [
                $this->getDocumentIteratorMock(
                    $this->getAggregationIteratorMock(
                        [ $this->getValueAggregationMock('color', 2) ]
                    )
                ),
                $this->getFilterStateMock(true, 'color'),
            ],
            // Case #3, inactive filter state, no choice, document returns empty AggregationIterator.
            [
                $this->getDocumentIteratorMock(
                    $this->getAggregationIteratorMock(null)
                ),
                $this->getFilterStateMock(false),
            ],
        ];
    }

    /**
     * Tests getViewData method.
     *
     * @param DocumentIterator $documentIterator Document.
     * @param FilterState      $filterState      Filter state of ViewData class..
     *
     * @dataProvider getTestGetViewDataData
     */
    public function testGetViewData($documentIterator, $filterState)
    {
        $stc = new SingleTermChoice();
        $stc->setRequestField('choice');
        $stc->setSortType(['type' => '', 'order' => '', 'priorities' => ['country']]);

        $viewData = $stc->createViewData();
        $viewData->setName('testName');
        $viewData->setState($filterState);

        $stc->getViewData($documentIterator, $viewData);
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
        $mock->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue($isActive));
        if ($isActive) {
            $mock->expects($this->once())
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
