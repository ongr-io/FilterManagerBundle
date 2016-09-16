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

use ONGR\FilterManagerBundle\Filter\ViewData\AggregateViewData;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;

class AggregateViewDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests active and default values
     */
    public function testGettersAndSetters()
    {
        $data = new AggregateViewData();
        $choiceData = [
            new ChoicesAwareViewData(),
            new ChoicesAwareViewData(),
        ];
        $data->setItems($choiceData);
        $this->assertEquals(2, count($data->getItems()));
        $data->addItem(new ChoicesAwareViewData());
        $this->assertEquals(3, count($data->getItems()));
    }
}
