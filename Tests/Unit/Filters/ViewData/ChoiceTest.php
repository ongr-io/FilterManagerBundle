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

use ONGR\FilterManagerBundle\Filters\ViewData\Choice;

class ChoiceTest extends \PHPUnit_Framework_TestCase
{
    private $choice;

    protected function setUp()
    {
        $this->choice = new Choice();
    }

    public function testGetCount()
    {
        $this->choice->setCount(10);
        $this->assertEquals(10, $this->choice->getCount());

    }

    public function testGetLabel()
    {
        $this->choice->setLabel('test set label');
        $this->assertEquals('test set label', $this->choice->getLabel());

    }

    public function testGetUrlParameters()
    {
        $this->choice->setUrlParameters([]);
        $this->assertEquals([], $this->choice->getUrlParameters());
    }

    public function testGetMode()
    {
        $this->choice->setMode('test set mode');
        $this->assertEquals('test set mode', $this->choice->getMode());
    }

    public function testIsDefault()
    {
        $this->choice->setDefault(true);
        $this->assertEquals(true, $this->choice->isDefault());
    }

    public function testIsActive()
    {
        $this->choice->setActive(true);
        $this->assertEquals(true, $this->choice->isActive());
    }
}