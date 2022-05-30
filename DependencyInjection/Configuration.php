<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\DependencyInjection;

use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ParentNodeDefinitionInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ongr_filter_manager');
        $rootNode = $treeBuilder->getRootNode();

        $this->addManagersSection($rootNode);
        $this->addFiltersSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addManagersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('managers')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->info('Filter manager name')
                            ->end()
                            ->scalarNode('repository')
                                ->isRequired()
                                ->info('ElasticsearchBundle repository used for fetching data.')
                            ->end()
                            ->arrayNode('filters')
                                ->info('Filter names to include in manager.')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addFiltersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('filters')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->info('Filter name')->end()
                            ->scalarNode('type')->end()
                            ->scalarNode('document_field')->end()
                            ->scalarNode('request_field')->end()
                            ->arrayNode('tags')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('relations')
                                ->children()
                                    ->append($this->buildRelationsTree('search'))
                                    ->append($this->buildRelationsTree('reset'))
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Builds relations config tree for given relation name.
     *
     * @param string $relationType
     *
     * @return ArrayNodeDefinition
     */
    private function buildRelationsTree($relationType)
    {
        $filter = new ArrayNodeDefinition($relationType);

        $filter
            ->validate()
                ->ifTrue(
                    function ($v) {
                        return empty($v['include']) && empty($v['exclude']);
                    }
                )
                ->thenInvalid('Relation must have "include" or "exclude" fields specified.')
            ->end()
            ->validate()
                ->ifTrue(
                    function ($v) {
                        return !empty($v['include']) && !empty($v['exclude']);
                    }
                )
                ->thenInvalid('Relation must have only "include" or "exclude" fields specified.')
            ->end()
            ->children()
                ->arrayNode('include')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(
                            function ($v) {
                                return [$v];
                            }
                        )->end()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('exclude')
                    ->beforeNormalization()
                        ->ifString()
                        ->then(
                            function ($v) {
                                return [$v];
                            }
                        )
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $filter;
    }
}
