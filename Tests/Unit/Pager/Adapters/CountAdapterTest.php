<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Pager\Adapters;

use ONGR\FilterManagerBundle\Pager\Adapters\CountAdapter;

class CountAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetResults()
    {
        $adapter = new CountAdapter(0);
        $this->assertEquals([], $adapter->getResults(0, 0));
    }
}
