<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filters\Widget\Pager;

use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager;

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
        $filter = new Pager();

        $result = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($this->once())->method('getTotalCount')->willReturn(55);

        $viewData = $filter->createViewData();
        $viewData->setState(new FilterState());
        $viewData = $filter->getViewData($result, $viewData);

        $this->assertInstanceOf('ONGR\FilterManagerBundle\Filters\ViewData\PagerAwareViewData', $viewData);
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Pager\PagerService', $viewData->getPager());

        $this->assertEquals(55, $viewData->getPager()->getAdapter()->getTotalResults());
    }
}
