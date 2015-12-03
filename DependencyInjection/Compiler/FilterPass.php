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
use Symfony\Component\DependencyInjection\Reference;

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

                $filterName = $tag['filter_name'];
                $filterLabel = ONGRFilterManagerExtension::getFilterId($filterName);

                if (array_key_exists('manager', $tag)) {
                    $this->addFilter($container, $tag['manager'], $filterName, $filterId);
                }

                if ($filterLabel !== $filterId && !$container->has($filterLabel)) {
                    $container->setAlias($filterLabel, $filterId);
                }
            }
        }

        foreach ($container->findTaggedServiceIds('es.filter_manager') as $managerId => $managerTags) {
            $managerDefinition = $container->getDefinition($managerId);
            $this->checkManager($managerDefinition, "Manager '{$managerId}' does not have any filters.");
        }
    }

    /**
     * Adds filter to manager definition by id and name.
     *
     * @param ContainerBuilder $container
     * @param string           $managerName
     * @param string           $filterName
     * @param string           $filterId
     */
    private function addFilter($container, $managerName, $filterName, $filterId)
    {
        trigger_error(
            sprintf(
                'Manager `%s` assignation at filter\'s `%s` definition found. '
                . 'Filter should be added to manager at manager\'s configuration.',
                $managerName,
                $filterName
            ),
            E_USER_DEPRECATED
        );
        try {
            $managerDefinition = $container
                ->getDefinition(ONGRFilterManagerExtension::getFilterManagerId($managerName));
        } catch (InvalidArgumentException $e) {
            throw new InvalidConfigurationException(
                sprintf('Manager `%s` defined at filter `%s` configuration does not exist.', $managerName, $filterName)
            );
        }

        $filtersContainer = $managerDefinition->getArgument(0);
        $filtersContainer->addMethodCall(
            'set',
            [
                $filterName,
                new Reference($filterId),
            ]
        );
        $managerDefinition->replaceArgument(0, $filtersContainer);

        $container->setDefinition($managerName, $managerDefinition);
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
