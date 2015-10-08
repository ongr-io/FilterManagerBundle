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
use ONGR\FilterManagerBundle\Filters\ViewData\Choice;

class ChoicesAwareViewDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ChoicesAwareViewData
     */
    private $oChoicesAwareViewData;

    /**
     * Tests setUp method.
     */
    protected function setUp()
    {
        $this->oChoicesAwareViewData = new ChoicesAwareViewData();
    }

    /**
     * Tests testGetChoice method.
     */
    public function testGetChoice()
    {
        $this->oChoicesAwareViewData->setChoices(new Choice());
        $this->assertInstanceOf(
            'ONGR\FilterManagerBundle\Filters\ViewData\Choice',
            $this->oChoicesAwareViewData->getChoices()
        );
    }

    /**
     * Tests testAddChoice method.
     */
    public function testAddChoice()
    {
        $this->oChoicesAwareViewData->addChoice(new Choice());
        $this->assertInstanceOf(
            'ONGR\FilterManagerBundle\Filters\ViewData\Choice',
            $this->oChoicesAwareViewData->getChoices()[0]
        );
    }
}
