<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\Helper;

use ONGR\FilterManagerBundle\Filter\Helper\OptionsAwareTrait;

class OptionsAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testOptionsTraitStorage()
    {
        /** @var OptionsAwareTrait $obj */
        $obj = $this->getObjectForTrait('ONGR\FilterManagerBundle\Filter\Helper\OptionsAwareTrait');

        $obj->addOption('acme', 'foo');
        $this->assertEquals('foo', $obj->getOption('acme'));
        $this->assertTrue($obj->hasOption('acme'));

        $obj->removeOption('acme');
        $this->assertFalse($obj->hasOption('acme'));
    }

    public function testNotExistingOption()
    {
        /** @var OptionsAwareTrait $obj */
        $obj = $this->getObjectForTrait('ONGR\FilterManagerBundle\Filter\Helper\OptionsAwareTrait');

        $this->assertNull($obj->getOption('acme'));
        $this->assertEquals('bar', $obj->getOption('acme', 'bar'));
    }

    public function testOptionsSet()
    {
        /** @var OptionsAwareTrait $obj */
        $obj = $this->getObjectForTrait('ONGR\FilterManagerBundle\Filter\Helper\OptionsAwareTrait');

        $options = [
            'acme' => 'foo',
            'bar' => 'do',
        ];

        $obj->setOptions($options);
        $this->assertEquals('foo', $obj->getOption('acme'));
        $this->assertEquals('do', $obj->getOption('bar'));
        $this->assertSame($options, $obj->getOptions());
    }
}
