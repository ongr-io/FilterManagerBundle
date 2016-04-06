<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Event;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Event\SearchResponseEvent;

class SearchResponseEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SearchResponseEvent
     */
    private $event;

    /**
     * @var DocumentIterator
     */
    private $result;
    
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var DocumentIterator $result */
        $this->result = $this->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->event = new SearchResponseEvent($this->result);
    }

    public function testConstruct()
    {
        $this->assertEquals($this->event, new SearchResponseEvent($this->result));
    }

    public function testGetDocumentIterator()
    {
        $this->assertEquals($this->result, $this->event->getDocumentIterator());
    }
}
