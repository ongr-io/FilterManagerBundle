<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filters\ViewData;

use ONGR\FilterManagerBundle\Filters\ViewData\ChoicesAwareViewData;

class ChoicesAwareViewDataTest extends \PHPUnit_Framework_TestCase
{
    private $oChoicesAwareViewData;
    private $mockChoice;

    protected function setUp()
    {
        $this->oChoicesAwareViewData = new ChoicesAwareViewData();
        $this->mockChoice = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\ViewData\Choice')->getMock();
    }

    public function testGetChoice()
    {
        $this->oChoicesAwareViewData->setChoices($this->mockChoice);
        $this->assertEquals($this->mockChoice, $this->oChoicesAwareViewData->getChoices());
    }

    public function testAddChoice()
    {
        $this->oChoicesAwareViewData->addChoice($this->mockChoice);
        $this->assertEquals($this->mockChoice, $this->oChoicesAwareViewData->getChoices()[0]);
    }
}