<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filters\Range;

use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Widget\Range\Range;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit test for range filter.
 */
class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check if getState returns false when parameters are malformed.
     */
    public function testGetState()
    {
        $filter = new Range();
        $filter->setRequestField('range');
        $state = $filter->getState(new Request(['range' => 'testValue']));

        $this->assertFalse($state->isActive());
    }

    /**
     * Check if getState doesn't change value if filter isn't active.
     */
    public function testGetStateInactive()
    {
        $filter = new Range();
        $filter->setRequestField('range');
        $state = $filter->getState(new Request([]));

        $expectedState = new FilterState();
        $this->assertEquals($expectedState, $state);
    }
}
