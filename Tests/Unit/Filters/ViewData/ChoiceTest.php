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
    /**
     * @var Choice
     */
    private $choice;

    /**
     * Tests setUp method.
     */
    protected function setUp()
    {
        $this->choice = new Choice();
    }

    /**
     * Tests testGetCount method.
     */
    public function testGetCount()
    {
        $this->choice->setCount(10);
        $this->assertEquals(10, $this->choice->getCount());
    }

    /**
     * Tests testGetLabel method.
     */
    public function testGetLabel()
    {
        $this->choice->setLabel('test set label');
        $this->assertEquals('test set label', $this->choice->getLabel());
    }

    /**
     * Tests testGetUrlParameters method.
     */
    public function testGetUrlParameters()
    {
        $this->choice->setUrlParameters(['http://google.com']);
        $this->assertEquals(['http://google.com'], $this->choice->getUrlParameters());
    }

    /**
     * Tests testGetMode method.
     */
    public function testGetMode()
    {
        $this->choice->setMode('test set mode');
        $this->assertEquals('test set mode', $this->choice->getMode());
    }

    /**
     * Tests testIsDefault method.
     */
    public function testIsDefault()
    {
        $this->choice->setDefault(true);
        $this->assertEquals(true, $this->choice->isDefault());
    }

    /**
     * Tests testIsActive method.
     */
    public function testIsActive()
    {
        $this->choice->setActive(true);
        $this->assertEquals(true, $this->choice->isActive());
    }
}
