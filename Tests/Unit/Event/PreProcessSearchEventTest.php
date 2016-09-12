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

use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\Widget\Range\Range;
use ONGR\FilterManagerBundle\Event\PreProcessSearchEvent;

class PreProcessSearchEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PreProcessSearchEvent
     */
    private $event;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->event = new PreProcessSearchEvent(new Range(), new Search());
    }

    public function testConstruct()
    {
        $this->assertEquals($this->event, new PreProcessSearchEvent(new Range(), new Search()));
    }

    public function testGetters()
    {
        $this->assertEquals(new Search(), $this->event->getRelatedSearch());
        $this->assertEquals(new Range(), $this->event->getFilter());
    }
}
