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
    /**
     * @var ChoicesAwareViewData
     */
    private $oChoicesAwareViewData;

    /**
     * @var \ONGR\FilterManagerBundle\Filters\ViewData\Choice
     */
    private $mockChoice;

    /**
     * Tests setUp method.
     */
    protected function setUp()
    {
        $this->oChoicesAwareViewData = new ChoicesAwareViewData();
        $this->mockChoice = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\ViewData\Choice')->getMock();
    }

    /**
     * Tests testGetChoice method.
     */
    public function testGetChoice()
    {
        $this->oChoicesAwareViewData->setChoices($this->mockChoice);
        $this->assertEquals($this->mockChoice, $this->oChoicesAwareViewData->getChoices());
    }

    /**
     * Tests testAddChoice method.
     */
    public function testAddChoice()
    {
        $this->oChoicesAwareViewData->addChoice($this->mockChoice);
        $this->assertEquals($this->mockChoice, $this->oChoicesAwareViewData->getChoices()[0]);
    }
}
