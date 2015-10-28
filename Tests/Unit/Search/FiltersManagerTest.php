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

use ONGR\FilterManagerBundle\Search\FiltersManager;
use ONGR\FilterManagerBundle\Search\SearchRequest;

class FiltersManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests execute method.
     */
    public function testExecute()
    {
        $mockFilterState = $this->getMock('ONGR\FilterManagerBundle\Filters\FilterState');
        $mockFilterState->expects($this->any())
            ->method('getURLParameters')
            ->will($this->returnValue([]));

        $mockFilterInterface = $this->getMock('ONGR\FilterManagerBundle\Filters\FilterInterface');
        $mockFilterInterface->expects($this->once())
            ->method('preProcessSearch');
        $mockFilterInterface->expects($this->once())
            ->method('getSearchRelation')
            ->will($this->returnValue(null));

        $searchRequest = new SearchRequest();
        $searchRequest->set('filter', $mockFilterState);

        $mockFiltersContainer = $this->getMock('ONGR\FilterManagerBundle\Search\FiltersContainer');
        $mockFiltersContainer->expects($this->once())
            ->method('buildSearchRequest')
            ->will($this->returnValue($searchRequest));
        $mockFiltersContainer->expects($this->exactly(2))
            ->method('buildSearch')
            ->will($this->returnValue($this->getMock('ONGR\ElasticsearchDSL\Search')));
        $mockFiltersContainer->expects($this->exactly(2))
            ->method('all')
            ->will($this->returnValue(['filter' => $mockFilterInterface]));
        $mockFiltersContainer->expects($this->once())
            ->method('getFiltersByRelation')
            ->will($this->returnValue(null));

        $mockDocumentIterator = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRepository = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Repository')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRepository->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($mockDocumentIterator));

        $filtersManager = new FiltersManager(
            $mockFiltersContainer,
            $mockRepository
        );
        $filtersManager->execute($this->getMock('Symfony\Component\HttpFoundation\Request'));
    }
}
