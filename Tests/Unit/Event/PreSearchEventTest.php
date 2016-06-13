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

use ONGR\FilterManagerBundle\Event\PreSearchEvent;
use ONGR\FilterManagerBundle\Search\SearchRequest;

class PreSearchEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PreSearchEvent
     */
    private $event;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->event = new PreSearchEvent(new SearchRequest(['test' => 'test']));
    }

    public function testConstruct()
    {
        $this->assertEquals($this->event, new PreSearchEvent(new SearchRequest(['test' => 'test'])));
    }

    public function testGetSearchRequest()
    {
        $this->assertEquals(new SearchRequest(['test' => 'test']), $this->event->getSearchRequest());
    }
}
