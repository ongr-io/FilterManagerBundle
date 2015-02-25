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
    /**
     * Check if correct results number is returned.
     */
    public function testGetTotalResults()
    {
        $adapter = new CountAdapter(79);
        $this->assertEquals(79, $adapter->getTotalResults());
    }

    /**
     * Check if an empty array is returned (count adapter doesn't contain items).
     */
    public function testGetResults()
    {
        $adapter = new CountAdapter(58);
        $this->assertEmpty($adapter->getResults(0, PHP_INT_MAX));
    }
}
