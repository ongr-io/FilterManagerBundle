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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ongr_filter_manager');

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
                            ->arrayNode('filters')
                                ->info('Filter names to include in manager.')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('repository')
                                ->isRequired()
                                ->info('ElasticsearchBundle repository used for fetching data.')
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
                    ->validate()
                        ->ifTrue(
                            function ($v) {
                                $v = array_filter($v);

                                return empty($v);
                            }
                        )
                        ->thenInvalid('At least single filter must be configured.')
                    ->end()
                    ->children()
                        ->append($this->buildFilterTree('choice'))
                        ->append($this->buildFilterTree('multi_choice'))
                        ->append($this->buildFilterTree('match'))
                        ->append($this->buildFilterTree('fuzzy'))
                        ->append($this->buildFilterTree('sort'))
                        ->append($this->buildFilterTree('pager'))
                        ->append($this->buildFilterTree('range'))
                        ->append($this->buildFilterTree('date_range'))
                        ->append($this->buildFilterTree('field_value'))
                        ->append($this->buildFilterTree('document_value'))
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Builds filter config tree for given filter name.
     *
     * @param string $filterName
     *
     * @return ArrayNodeDefinition
     */
    private function buildFilterTree($filterName)
    {
        $filter = new ArrayNodeDefinition($filterName);

        /** @var ParentNodeDefinitionInterface $node */
        $node = $filter
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('name')->end()
                    ->arrayNode('relations')
                        ->children()
                            ->append($this->buildRelationsTree('search'))
                            ->append($this->buildRelationsTree('reset'))
                        ->end()
                    ->end()
                    ->scalarNode('field')
                        ->info('Document field name.')
                    ->end()
                    ->arrayNode('tags')
                        ->info('Filter tags that will be passed to view data.')
                        ->prototype('scalar')->end()
                    ->end()
                ->end();

        if ($filterName != 'field_value') {
            $node
                ->children()
                    ->scalarNode('request_field')
                        ->info('URL parameter name.')
                        ->isRequired()
                    ->end()
                ->end();
        }

        switch ($filterName) {
            case 'choice':
            case 'multi_choice':
                $node
                    ->children()
                        ->integerNode('size')
                            ->info('Result size to return.')
                        ->end()
                        ->arrayNode('sort')
                        ->children()
                            ->enumNode('type')
                                ->values(['_term', '_count'])
                                ->defaultValue('_term')
                            ->end()
                            ->enumNode('order')
                                ->values(['asc', 'desc'])
                                ->defaultValue('asc')
                            ->end()
                            ->arrayNode('priorities')->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end();
                break;
            case 'match':
                $node
                    ->children()
                        ->scalarNode('operator')
                            ->info('The operator flag.')
                        ->end()
                        ->scalarNode('fuzziness')
                            ->info('The maximum edit distance.')
                        ->end()
                    ->end();
                break;
            case 'fuzzy':
                $node
                    ->children()
                        ->scalarNode('fuzziness')
                            ->info('The maximum edit distance.')
                        ->end()
                        ->integerNode('prefix_length')
                            ->info(
                                'The number of initial characters which will not be “fuzzified”.
                                This helps to reduce the number of terms which must be examined.'
                            )
                        ->end()
                        ->integerNode('max_expansions')
                            ->info('The maximum number of terms that the fuzzy query will expand to.')
                        ->end()
                    ->end();
                break;
            case 'sort':
                $node
                    ->children()
                        ->arrayNode('choices')
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->always(
                                        function ($v) {
                                            if (empty($v['fields']) && !empty($v['field'])) {
                                                $field = ['field' => $v['field']];
                                                if (array_key_exists('order', $v)) {
                                                    $field['order'] = $v['order'];
                                                }
                                                if (array_key_exists('mode', $v)) {
                                                    $field['mode'] = $v['mode'];
                                                }
                                                $v['fields'][] = $field;
                                            }

                                            if (empty($v['label'])) {
                                                $v['label'] = $v['fields'][0]['field'];
                                            }

                                            return $v;
                                        }
                                    )
                                ->end()
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('label')->end()
                                    ->scalarNode('field')->end()
                                    ->scalarNode('order')->defaultValue('asc')->end()
                                    ->scalarNode('mode')->defaultNull()->end()
                                    ->scalarNode('key')->info('Custom parameter value')->end()
                                    ->booleanNode('default')->defaultFalse()->end()
                                    ->arrayNode('fields')
                                        ->isRequired()
                                        ->requiresAtLeastOneElement()
                                        ->prototype('array')
                                        ->children()
                                            ->scalarNode('field')->isRequired()->end()
                                            ->scalarNode('order')->defaultValue('asc')->end()
                                            ->scalarNode('mode')->defaultNull()->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end();
                break;
            case 'pager':
                $node
                    ->children()
                        ->integerNode('count_per_page')
                            ->info('Item count per page')
                            ->defaultValue(10)
                        ->end()
                        ->integerNode('max_pages')
                            ->info('Max pages displayed in pager at once.')
                            ->defaultValue(8)
                        ->end()
                    ->end();
                break;
            case 'range':
            case 'date_range':
                $node
                    ->children()
                        ->booleanNode('inclusive')
                            ->info('Whether filter should match range ends.')
                            ->defaultFalse()
                        ->end()
                    ->end();
                break;
            case 'field_value':
                $node
                    ->children()
                        ->scalarNode('value')
                            ->info('Value which will be used for filtering.')
                            ->isRequired()
                    ->end();
                break;
            case 'document_value':
                $node
                    ->children()
                        ->scalarNode('document_field')
                            ->info('Field name from document object to pass to the filter.')
                            ->isRequired()
                    ->end();
                break;
            default:
                // Default config is enough.
                break;
        }

        return $filter;
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
