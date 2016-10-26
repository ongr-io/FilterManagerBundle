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
    public function testGetSearchRequest()
    {
        /** @var SearchRequest $request */
        $request = $this->getMock('ONGR\FilterManagerBundle\Search\SearchRequest');
        $event = new PreSearchEvent($request);

        $this->assertSame($request, $event->getSearchRequest());
    }
}
