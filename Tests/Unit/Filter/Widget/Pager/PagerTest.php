<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\Widget\Pager;

use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Widget\Pager\Pager;

/**
 * Unit tests for pager.
 */
class PagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check if preProcessSearch does nothing, as expected.
     */
    public function testPreProcessSearch()
    {
        $filter = new Pager();

        $originalSearch = new Search();
        $originalRelatedSearch = new Search();
        $originalState = new FilterState();

        $search = clone $originalSearch;
        $relatedSearch = clone $originalRelatedSearch;
        $state = clone $originalState;

        $filter->preProcessSearch($search, $relatedSearch, $state);

        $this->assertEquals($originalSearch, $search);
        $this->assertEquals($originalRelatedSearch, $relatedSearch);
        $this->assertEquals($originalState, $state);
    }

    /**
     * Test for getViewData().
     */
    public function testGetViewData()
    {
        $filterStateMock = $this->getMockBuilder('ONGR\FilterManagerBundle\Filter\FilterState')->getMock();
        $filterStateMock->expects($this->any())->method('getValue')->willReturn(1);
        $filter = new Pager();
        $filter->addOption('max_pages', 1);
        $filter->addOption('count_per_page', 2);
        $result = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($this->once())->method('count')->willReturn(55);

        $viewData = $filter->createViewData();
        $viewData->setState($filterStateMock);
        $viewData = $filter->getViewData($result, $viewData);

        $this->assertInstanceOf('ONGR\FilterManagerBundle\Filter\ViewData\PagerAwareViewData', $viewData);
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Pager\PagerService', $viewData->getPager());

        $this->assertEquals(55, $viewData->getPager()->getAdapter()->getTotalResults());
    }
}
