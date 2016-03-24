<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\ViewData\Choice;

class ChoiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests active and default values
     */
    public function testActiveAndDefault()
    {
        $choice = new Choice();
        $choice->setActive(true);
        $choice->setDefault(false);
        $this->assertTrue($choice->isActive());
        $this->assertFalse($choice->isDefault());
    }

    /**
     * Tests Mode
     */
    public function testMode()
    {
        $choice = new Choice();
        $choice->setMode('mode');
        $this->assertEquals('mode', $choice->getMode());
    }
}
