<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\DependencyInjection\Compiler;

use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Compiles custom filters.
 */
class FilterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->findTaggedServiceIds('ongr_filter_manager.filter') as $filterId => $filterTags) {
            foreach ($filterTags as $tag) {
                if (!array_key_exists('filter_name', $tag)) {
                    throw new InvalidConfigurationException(
                        sprintf('Filter tagged with `%s` must have `filter_name` set.', $filterId)
                    );
                }

                $filterLabel = ONGRFilterManagerExtension::getFilterId($tag['filter_name']);
                if ($filterLabel === $filterId) {
                    continue;
                }

                if ($container->has($filterLabel)) {
                    throw new InvalidConfigurationException(
                        "Found duplicate filter name `{$tag['filter_name']}`"
                    );
                }
                $container->setAlias($filterLabel, $filterId);
            }
        }

        foreach ($container->findTaggedServiceIds('es.filter_manager') as $managerId => $managerTags) {
            $managerDefinition = $container->getDefinition($managerId);
            $this->checkManager($managerDefinition, "Manager '{$managerId}' does not have any filters.");
        }
    }

    /**
     * Checks if manager definition has any filters set.
     *
     * @param Definition $filtersManager
     * @param string     $message
     *
     * @throws InvalidArgumentException
     */
    private function checkManager(Definition $filtersManager, $message = '')
    {
        $filtersContainer = $filtersManager->getArgument(0);
        if (!$filtersContainer->hasMethodCall('set')) {
            throw new InvalidArgumentException($message);
        }
    }
}
