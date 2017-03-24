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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
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
        foreach ($container->findTaggedServiceIds('ongr_filter_manager.filter') as $filterId => $filterTags) {
            $tagOptions = array_shift($filterTags);

            if (!array_key_exists('type', $tagOptions)) {
                throw new InvalidConfigurationException(
                    sprintf('Filter service `%s` must have `type` set.', $filterId)
                );
            }

            $filters[$tagOptions['type']] = $filterId;
        }

        foreach ($container->getParameter('ongr_filter_manager.filters') as $filterName => $filterOptions) {
            if (!array_key_exists($filterOptions['type'], $filters)) {
                throw new InvalidConfigurationException(
                    "There is no `{$filterOptions['type']}` type filter registered."
                );
            }

            if (class_exists('Symfony\Component\DependencyInjection\ChildDefinition')) {
                $definition = new ChildDefinition($filters[($filterOptions['type'])]);
            } else {
                $definition = new DefinitionDecorator($filters[($filterOptions['type'])]);
            }
            $definition->addMethodCall('setRequestField', [$filterOptions['request_field']]);
            $definition->addMethodCall('setDocumentField', [$filterOptions['document_field']]);
            $definition->addMethodCall('setTags', [$filterOptions['tags']]);
            $definition->addMethodCall('setOptions', [$filterOptions['options']]);
            $this->addRelation($definition, $filterOptions, 'search', 'include');
            $this->addRelation($definition, $filterOptions, 'search', 'exclude');
            $this->addRelation($definition, $filterOptions, 'reset', 'include');
            $this->addRelation($definition, $filterOptions, 'reset', 'exclude');

            $container->setDefinition(ONGRFilterManagerExtension::getFilterId($filterName), $definition);
        }

        foreach ($container->getParameter('ongr_filter_manager.managers') as $managerName => $managerOptions) {
            $filterContainer = new Definition('ONGR\FilterManagerBundle\Search\FilterContainer');

            if (isset($managerOptions['filters'])) {
                foreach ($managerOptions['filters'] as $filter) {
                    $filterContainer->addMethodCall(
                        'set',
                        [$filter, new Reference(ONGRFilterManagerExtension::getFilterId($filter))]
                    );
                }
            }

            $managerDefinition = new Definition(
                'ONGR\FilterManagerBundle\Search\FilterManager',
                [
                    $filterContainer,
                    new Reference($managerOptions['repository']),
                    new Reference('event_dispatcher'),
                    new Reference('jms_serializer')
                ]
            );

            $container->setDefinition(ONGRFilterManagerExtension::getFilterManagerId($managerName), $managerDefinition);
        }
    }

    /**
     * Adds relation to filter.
     *
     * @param Definition $definition
     * @param array      $filter
     * @param string     $urlType
     * @param string     $relationType
     */
    private function addRelation(Definition $definition, $filter, $urlType, $relationType)
    {
        if (empty($filter['relations'][$urlType][$relationType])) {
            return;
        }

        $relation = new Definition(
            sprintf('ONGR\FilterManagerBundle\Relation\%sRelation', ucfirst($relationType)),
            [$filter['relations'][$urlType][$relationType]]
        );
        $definition->addMethodCall(
            'set' . ucfirst($urlType) . 'Relation',
            [$relation]
        );
    }
}
