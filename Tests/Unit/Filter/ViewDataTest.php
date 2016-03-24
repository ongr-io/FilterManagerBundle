<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter;

use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;

class ViewDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for getSerializableData()
     */
    public function testGetSerializableData()
    {
        $viewData = new ViewData();
        $viewData->setName('Title');
        $viewData->setTags(['tag1', 'tag2']);
        $viewData->setUrlParameters(['title' => 'foo']);
        $viewData->setResetUrlParameters(['field2' => 'val2']);

        $state = new FilterState();
        $state->setName('title');
        $state->setActive(true);
        $state->setValue('foo');
        $viewData->setState($state);

        $expected = [
            'name' => 'Title',
            'tags' => ['tag1', 'tag2'],
            'url_params' => ['title' => 'foo'],
            'reset_url_params' => ['field2' => 'val2'],
            'state' => [
                'active' => true,
                'value' => 'foo',
            ],
        ];

        $this->assertEquals($expected, $viewData->getSerializableData());
    }

    /**
     * Tests Tags getter and setter
     */
    public function testTagGetterAndSetter()
    {
        $viewData = new ViewData();
        $viewData->setTags('tags');
        $this->assertEquals('tags', $viewData->getTags());
    }
}
