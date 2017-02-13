<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Search;

use ONGR\FilterManagerBundle\Search\FilterManager;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests handleRequest() method.
     */
    public function testHandleRequest()
    {
        $mockFilterState = $this->createMock('ONGR\FilterManagerBundle\Filter\FilterState');
        $mockFilterState->expects($this->any())
            ->method('getURLParameters')
            ->will($this->returnValue([]));

        $mockFilterInterface = $this->createMock('ONGR\FilterManagerBundle\Filter\FilterInterface');
        $mockFilterInterface->expects($this->once())
            ->method('preProcessSearch');
        $mockFilterInterface->expects($this->once())
            ->method('isRelated')
            ->will($this->returnValue(true));
        $mockFilterInterface->expects($this->once())
            ->method('getSearchRelation')
            ->will($this->returnValue(null));

        $searchRequest = new SearchRequest();
        $searchRequest->set('filter', $mockFilterState);

        $mockFilterContainer = $this->createMock('ONGR\FilterManagerBundle\Search\FilterContainer');
        $mockFilterContainer->expects($this->once())
            ->method('buildSearchRequest')
            ->will($this->returnValue($searchRequest));
        $mockFilterContainer->expects($this->exactly(2))
            ->method('buildSearch')
            ->will($this->returnValue($this->createMock('ONGR\ElasticsearchDSL\Search')));
        $mockFilterContainer->expects($this->exactly(2))
            ->method('all')
            ->will($this->returnValue(['filter' => $mockFilterInterface]));
        $mockFilterContainer->expects($this->once())
            ->method('getFiltersByRelation')
            ->will($this->returnValue(null));

        $mockDocumentIterator = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRepository = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Repository')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRepository->expects($this->once())
            ->method('findDocuments')
            ->will($this->returnValue($mockDocumentIterator));

        $filterManager = new FilterManager(
            $mockFilterContainer,
            $mockRepository,
            new EventDispatcher()
        );
        $filterManager->handleRequest($this->createMock('Symfony\Component\HttpFoundation\Request'));
    }
}
