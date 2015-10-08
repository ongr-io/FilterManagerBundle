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
    private $countAdapter;

    protected function setUp()
    {
        $this->countAdapter = new CountAdapter(10);
    }

    public function testGetTotalResult()
    {

        $result = $this->countAdapter->getTotalResults();
        $this->assertEquals(10, $result);
    }

    public function testGetResults()
    {
        $result = $this->countAdapter->getResults(0,10);
        $this->assertEquals([], $result);
    }
}
