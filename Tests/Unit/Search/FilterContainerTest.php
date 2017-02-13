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

use ONGR\FilterManagerBundle\Search\FilterContainer;

class FilterContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getFiltersByRelation method.
     */
    public function testGetFiltersByRelation()
    {
        $filterContainer = new FilterContainer();
        $result = $filterContainer->getFiltersByRelation(
            $this->createMock('ONGR\FilterManagerBundle\Relation\RelationInterface')
        );
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Relation\FilterIterator', $result);
    }

    /**
     * Tests buildSearchRequest method.
     */
    public function testBuildSearchRequest()
    {
        $filterContainer = new FilterContainer();
        $filterContainer->add(
            [
                $this->getFilterInterfaceMock(),
                $this->getFilterInterfaceMock(),
            ]
        );

        $result = $filterContainer->buildSearchRequest(
            $this->createMock('Symfony\Component\HttpFoundation\Request')
        );

        $this->assertEquals(2, count($result));
    }

    /**
     * Tests buildSearch method.
     */
    public function testBuildSearch()
    {
        $mockRequest = $this->createMock('ONGR\FilterManagerBundle\Search\SearchRequest');
        $mockRequest->expects($this->once())
            ->method('get')
            ->will($this->returnValue(null));

        $mockFilterInterface = $this->createMock('ONGR\FilterManagerBundle\Filter\FilterInterface');
        $mockFilterInterface->expects($this->once())
            ->method('modifySearch')
            ->withConsecutive(
                $this->isInstanceOf('ONGR\ElasticsearchDSL\Search'),
                $this->equalTo('testName'),
                $this->identicalTo($mockRequest)
            );

        $filterContainer = new FilterContainer();
        $filterContainer->add([ $mockFilterInterface ]);

        $result = $filterContainer->buildSearch($mockRequest);
        $this->assertInstanceOf('ONGR\ElasticsearchDSL\Search', $result);
    }

    /**
     * Return a mock object of FilterInterface.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFilterInterfaceMock()
    {
        $mock = $this->getMockBuilder('ONGR\FilterManagerBundle\Filter\FilterInterface')->getMock();
        $mock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue($this->createMock('ONGR\FilterManagerBundle\Filter\FilterState')));

        return $mock;
    }
}
