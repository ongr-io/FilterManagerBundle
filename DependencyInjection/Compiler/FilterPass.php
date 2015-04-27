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

use ONGR\FilterManagerBundle\Filters\FilterInterface;
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
        $filters = [];
        foreach ($container->findTaggedServiceIds('es.filter_manager') as $managerId => $managerTags) {
            $managerDefinition = $container->getDefinition($managerId);
            foreach ($container->findTaggedServiceIds('ongr_filter_manager.filter') as $filterId => $filterTags) {
                if (array_key_exists('manager', $filterTags[0])
                    && strrpos(
                        $managerId,
                        $filterTags[0]['manager'],
                        strlen($filterTags[0]['manager'])
                    )
                ) {
                    if (!array_key_exists('filter_name', $filterTags[0])) {
                        throw new InvalidArgumentException(
                            "Filters tagged with 'ongr_filter_manager.filter' must have 'filter_name' parameter."
                        );
                    }

                    if (($container->get($filterId) instanceof FilterInterface) === false) {
                        throw new InvalidArgumentException("Service {$filterId} must implement FilterInterface.");
                    }

                    $filters[] = $filterId;
                    $filterName = $filterTags[0]['filter_name'];

                    $filtersContainer = $managerDefinition->getArgument(0);
                    $filtersContainer->addMethodCall(
                        'set',
                        [$filterName, new Reference($filterId)]
                    );
                    $managerDefinition->replaceArgument(0, $filtersContainer);
                    $container->setDefinition($managerId, $managerDefinition);
                }
            }
            /** @var Definition $filtersContainer */
            $filtersContainer = $managerDefinition->getArgument(0);
            if (!$filtersContainer->hasMethodCall('set')) {
                throw new InvalidArgumentException("Manager '{$managerId}' does not have any filters.");
            }
        }
    }
}
