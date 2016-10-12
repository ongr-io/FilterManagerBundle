<?php

/*
 * This file is part of the ONGR package.
 *
 * Copyright (c) 2014-2015 NFQ Technologies UAB
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\DependencyInjection\Compiler;

use ONGR\FilterManagerBundle\DependencyInjection\Compiler\FilterPass;
use ONGR\FilterManagerBundle\Tests\app\fixture\TestBundle\Filter\FooRange\FooRange;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FilterPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * Before a test method is run, a template method called setUp() is invoked.
     */
    public function setUp()
    {
    }

    public function testPass()
    {
        $this->assertTrue(true);
    }
}
