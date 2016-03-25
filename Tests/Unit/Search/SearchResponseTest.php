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

use ONGR\FilterManagerBundle\Search\SearchResponse;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Tests\app\fixture\TestBundle\Document\Person;

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
        $filters = $this->getMock('NGR\FilterManagerBundle\Filter\ViewData');
        $result = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $result->expects($this->any())->method('valid')->will($this->returnValue(true));
        $result->expects($this->any())->method('current')->will($this->returnValue(new \stdClass()));
        $response = new SearchResponse($filters, $result, []);
        $response->getSerializableData();
    }
}
