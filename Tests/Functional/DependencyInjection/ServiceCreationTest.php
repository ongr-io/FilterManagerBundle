<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\DependencyInjection;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager;
use ONGR\FilterManagerBundle\Filters\Widget\Range\Range;

class ServiceCreationTest extends ElasticsearchTestCase
{
    /**
     * Test for filter services registration.
     */
    public function testServices()
    {
        $container = self::createClient()->getContainer();

        // Test if filter was registered correctly.
        $this->assertTrue($container->has('ongr_filter_manager.filter.phrase'));
        $this->assertInstanceOf(
            'ONGR\FilterManagerBundle\Filters\Widget\Search\MatchSearch',
            $container->get('ongr_filter_manager.filter.phrase')
        );

        // Test if pager filter was registered correctly.
        $this->assertTrue($container->has('ongr_filter_manager.filter.pager'));
        /** @var Pager $pager */
        $pager = $container->get('ongr_filter_manager.filter.pager');
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager', $pager);
        $this->assertEquals(12, $pager->getCountPerPage());
        $this->assertEquals('page', $pager->getRequestField());

        // Test if range filter was registered correctly.
        $this->assertTrue($container->has('ongr_filter_manager.filter.range'));
        /** @var Range $range */
        $range = $container->get('ongr_filter_manager.filter.range');
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Filters\Widget\Range\Range', $range);
        $this->assertEquals('price', $range->getField());
        $this->assertEquals('range', $range->getRequestField());

        // Test if filter manager was registered correctly.
        $this->assertTrue($container->has('ongr_filter_manager.foo_filters'));
        $this->assertInstanceOf(
            'ONGR\FilterManagerBundle\Search\FiltersManager',
            $container->get('ongr_filter_manager.foo_filters')
        );
    }
}
