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

use ONGR\FilterManagerBundle\Search\FiltersContainer;

class FiltersContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getFiltersByRelation method.
     */
    public function testGetFiltersByRelation()
    {
        $filtersContainer = new FiltersContainer();
        $result = $filtersContainer->getFiltersByRelation(
            $this->getMock('ONGR\FilterManagerBundle\Relations\RelationInterface')
        );
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Relations\FilterIterator', $result);
    }

    /**
     * Tests buildSearchRequest method.
     */
    public function testBuildSearchRequest()
    {
        $filtersContainer = new FiltersContainer();
        $filtersContainer->add(
            [
                $this->getFilterInterfaceMock(),
                $this->getFilterInterfaceMock(),
            ]
        );

        $result = $filtersContainer->buildSearchRequest(
            $this->getMock('Symfony\Component\HttpFoundation\Request')
        );

        $this->assertEquals(2, count($result));
    }

    /**
     * Tests buildSearch method.
     */
    public function testBuildSearch()
    {
        $mockRequest = $this->getMock('ONGR\FilterManagerBundle\Search\SearchRequest');
        $mockRequest->expects($this->once())
            ->method('get')
            ->will($this->returnValue(null));

        $mockFilterInterface = $this->getMock('ONGR\FilterManagerBundle\Filters\FilterInterface');
        $mockFilterInterface->expects($this->once())
            ->method('modifySearch')
            ->withConsecutive(
                $this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Search'),
                $this->equalTo('testName'),
                $this->identicalTo($mockRequest)
            );

        $filtersContainer = new FiltersContainer();
        $filtersContainer->add([ $mockFilterInterface ]);

        $result = $filtersContainer->buildSearch($mockRequest);
        $this->assertInstanceOf('ONGR\ElasticsearchBundle\DSL\Search', $result);
    }

    /**
     * Return a mock object of FilterInterface.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilterInterfaceMock()
    {
        $mock = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\FilterInterface')->getMock();
        $mock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($this->getMock('ONGR\FilterManagerBundle\Filters\FilterState')));

        return $mock;
    }
}
