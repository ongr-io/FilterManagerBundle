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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages bundle configuration.
 */
class ONGRFilterManagerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $filterMap = $container->getParameter('ongr_filter_manager.filter_map');

        foreach ($config['filters'] as $type => $filters) {
            foreach ($filters as $name => $filter) {
                $filterDefinition = new Definition($filterMap[$type]);
                $filterDefinition->addMethodCall('setRequestField', [$filter['request_field']]);
                if (isset($filter['field'])) {
                    $filterDefinition->addMethodCall('setField', [$filter['field']]);
                }
                if (isset($filter['count_per_page'])) {
                    $filterDefinition->addMethodCall('setCountPerPage', [$filter['count_per_page']]);
                }
                if (isset($filter['max_pages'])) {
                    $filterDefinition->addMethodCall('setMaxPages', [$filter['max_pages']]);
                }
                if (isset($filter['choices'])) {
                    $filterDefinition->addMethodCall('setChoices', [$filter['choices']]);
                }

                $this->addRelation($filterDefinition, $filter, 'search', 'include');
                $this->addRelation($filterDefinition, $filter, 'search', 'exclude');
                $this->addRelation($filterDefinition, $filter, 'reset', 'include');
                $this->addRelation($filterDefinition, $filter, 'reset', 'exclude');

                $container->setDefinition(sprintf('ongr_filter_manager.filter.%s', $name), $filterDefinition);
            }
        }

        foreach ($config['managers'] as $name => $manager) {
            $filtersContainer = new Definition('ONGR\FilterManagerBundle\Search\FiltersContainer');

            foreach ($manager['filters'] as $filter) {
                $filtersContainer->addMethodCall(
                    'set',
                    [$filter, new Reference(sprintf('ongr_filter_manager.filter.%s', $filter))]
                );
            }

            $managerDefinition = new Definition(
                'ONGR\FilterManagerBundle\Search\FiltersManager',
                [
                    $filtersContainer,
                    new Reference(sprintf('es.manager.%s.%s', $config['es_manager'], $manager['repository']))
                ]
            );

            $container->setDefinition(sprintf('ongr_filter_manager.%s', $name), $managerDefinition);
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
    protected function addRelation(Definition $definition, $filter, $urlType, $relationType)
    {
        if (!empty($filter['relations'][$urlType][$relationType])) {
            $definition->addMethodCall(
                'set' . ucfirst($urlType) . 'Relation',
                [$this->getRelation($relationType, $filter['relations'][$urlType][$relationType])]
            );
        }
    }

    /**
     * Creates relation definition by given parameters.
     *
     * @param string $type
     * @param array  $relations
     *
     * @return Definition
     */
    protected function getRelation($type, $relations)
    {
        return new Definition(
            sprintf('ONGR\FilterManagerBundle\Relations\%sRelation', ucfirst($type)),
            [$relations]
        );
    }
}
