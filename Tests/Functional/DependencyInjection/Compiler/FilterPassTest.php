<?php

/*
 * This file is part of the ONGR package.
 *
 * Copyright (c) 2014-2015 NFQ Technologies UAB
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\DependencyInjection\Compiler;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;

class FilterPassTest extends ElasticsearchTestCase
{
    /**
     * Test if services are created by compiler pass.
     */
    public function testFilterImplementationException()
    {
        $container = self::createClient()->getContainer();

        $this->assertNotNull(
            $container->get('ongr_filter_manager.bar_filters'),
            "Manager 'bar_filters' should be defined."
        );

        $this->assertNotNull(
            $container->get('ongr_filter_manager.filter.bar_range'),
            "Filter 'foo_range' should be defined."
        );
    }
}
