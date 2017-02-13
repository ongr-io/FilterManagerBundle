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

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Search\SearchResponse;

class SearchResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the response when $document does not implement
     * SerializableInterface
     *
     * @expectedException \LogicException
     */
    public function testGetSerializableDataException()
    {
        /** @var ViewData $filters */
        $filters = $this->createMock('ONGR\FilterManagerBundle\Filter\ViewData');

        /** @var DocumentIterator $result */
        $result = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($this->any())->method('valid')->will($this->returnValue(true));
        $result->expects($this->any())->method('current')->will($this->returnValue(new \stdClass()));

        $response = new SearchResponse($filters, $result, []);
        $response->getSerializableData();
    }
}
