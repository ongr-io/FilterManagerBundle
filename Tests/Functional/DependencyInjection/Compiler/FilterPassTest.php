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

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Filters\FilterInterface;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FilterPassTest extends AbstractElasticsearchTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = self::createClient()->getContainer();
    }

    /**
     * DataProvider for test.
     *
     * @return array
     */
    public function filterTestProvider()
    {
        $fooRangeClass = 'ONGR\\FilterManagerBundle\\Tests\\app\\fixture\\Acme' .
            '\\TestBundle\\Filters\\FooRange\\FooRange';

        return [
            // Case #0. bar_filters.
            [
                'manager' => 'bar_filters',
                'filters' => [
                    'bar_range' => $fooRangeClass,
                    'sort' => 'ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort',
                    'inclusive_range' => 'ONGR\FilterManagerBundle\Filters\Widget\Range\Range',
                ],
            ],
            // Case #1. foo_filters.
            [
                'manager' => 'foo_filters',
                'filters' => [
                    'phrase' => 'ONGR\FilterManagerBundle\Filters\Widget\Search\MatchSearch',
                    'single_choice' => 'ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice',
                    'foo_range' => $fooRangeClass,
                ],
            ],
            // Case #2. Orphan filters.
            [
                'manager' => null,
                'filters' => [
                    'bar_range_no_manager' => $fooRangeClass,
                ],
            ],
        ];
    }

    /**
     * Test if services are created by compiler pass.
     *
     * @param string|null $manager
     * @param array       $filters
     *
     * @dataProvider filterTestProvider
     */
    public function testFilterImplementationException($manager, $filters)
    {
        // Check filter definitions.
        foreach ($filters as $filter => $class) {
            /** @var FilterInterface $filterInstance */
            $filterInstance = $this->container->get(
                ONGRFilterManagerExtension::getFilterId($filter),
                ContainerInterface::NULL_ON_INVALID_REFERENCE
            );
            $this->assertInstanceOf($class, $filterInstance);
        }
        if ($manager === null) {
            return;
        }

        /** @var FiltersManager $managerInstance */
        $managerInstance = $this->container->get(
            ONGRFilterManagerExtension::getFilterManagerId($manager),
            ContainerInterface::NULL_ON_INVALID_REFERENCE
        );
        $this->assertInstanceOf('ONGR\FilterManagerBundle\Search\FiltersManager', $managerInstance);
        $filterContainer = $this->getFilterContainer($managerInstance);
        $actualFilters = $filterContainer->all();
        $this->assertSameSize($filters, $actualFilters);
        foreach ($filters as $filter => $class) {
            $this->assertArrayHasKey($filter, $actualFilters);
            $this->assertInstanceOf($class, $actualFilters[$filter]);

            $filterInstance = $this->container->get(
                ONGRFilterManagerExtension::getFilterId($filter),
                ContainerInterface::NULL_ON_INVALID_REFERENCE
            );
            $this->assertSame($filterInstance, $actualFilters[$filter]);
        }
    }

    /**
     * @param FiltersManager $manager
     *
     * @return FiltersContainer
     */
    private function getFilterContainer($manager)
    {
        $class = new \ReflectionClass($manager);
        $property = $class->getProperty('container');
        $property->setAccessible(true);

        return $property->getValue($manager);
    }
}
