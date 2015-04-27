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

use ONGR\FilterManagerBundle\DependencyInjection\Filter\AbstractFilterFactory;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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
     * @var AbstractFilterFactory[]
     */
    protected $factories = [];
    
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        !isset($config['filters']) ? : $this->addFilters($config['filters'], $container);
        !isset($config['managers']) ? : $this->addFiltersManagers($config, $container);
    }

    /**
     * Adds filter factory.
     *
     * @param AbstractFilterFactory $factory
     */
    public function addFilterFactory(AbstractFilterFactory $factory)
    {
        $this->factories[$factory->getName()] = $factory;
    }

    /**
     * Formats filter service id from given name.
     *
     * @param string $name Filter manager name.
     *
     * @return string
     */
    public static function getFilterServiceId($name)
    {
        return sprintf('ongr_filter_manager.filter.%s', $name);
    }

    /**
     * Adds filters based on configuration.
     *
     * @param array            $config    Configuration.
     * @param ContainerBuilder $container Service container.
     */
    private function addFilters(array $config, ContainerBuilder $container)
    {
        $this->validateFilterNames($config);

        foreach ($config as $type => $filters) {
            foreach ($filters as $name => $config) {
                $filterDefinition = $this
                    ->getFilterFactory($type)
                    ->setConfiguration($config)
                    ->getDefinition();

                $this->addRelation($filterDefinition, $config, 'search', 'include');
                $this->addRelation($filterDefinition, $config, 'search', 'exclude');
                $this->addRelation($filterDefinition, $config, 'reset', 'include');
                $this->addRelation($filterDefinition, $config, 'reset', 'exclude');

                $container->setDefinition(self::getFilterServiceId($name), $filterDefinition);
            }
        }
    }

    /**
     * Checks if filter names are valid.
     *
     * @param array $filters Filters to validate.
     *
     * @throws InvalidConfigurationException
     */
    private function validateFilterNames(array $filters)
    {
        $existing = [];

        foreach ($filters as $type => $filters) {
            foreach ($filters as $name => $data) {
                if (in_array($name, $existing)) {
                    throw new InvalidConfigurationException(
                        "Found duplicate filter name `{$name}` in `{$type}` filter"
                    );
                }

                $existing[] = $name;
            }
        }
    }

    /**
     * Adds filters managers based on configuration.
     *
     * @param array            $config    Configuration array.
     * @param ContainerBuilder $container Service container.
     */
    private function addFiltersManagers(array $config, ContainerBuilder $container)
    {
        foreach ($config['managers'] as $name => $manager) {
            $filtersContainer = new Definition('ONGR\FilterManagerBundle\Search\FiltersContainer');

            foreach ($manager['filters'] as $filter) {
                $filtersContainer->addMethodCall(
                    'set',
                    [$filter, new Reference(self::getFilterServiceId($filter))]
                );
            }

            $managerDefinition = new Definition(
                'ONGR\FilterManagerBundle\Search\FiltersManager',
                [
                    $filtersContainer,
                    new Reference($manager['repository']),
                ]
            );
            $managerDefinition->addTag('es.filter_manager');

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
    private function addRelation(Definition $definition, $filter, $urlType, $relationType)
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
    private function getRelation($type, $relations)
    {
        return new Definition(
            sprintf('ONGR\FilterManagerBundle\Relations\%sRelation', ucfirst($type)),
            [$relations]
        );
    }

    /**
     * Returns filter factory.
     *
     * @param string $name Factory name.
     *
     * @return AbstractFilterFactory
     *
     * @throws InvalidConfigurationException Invaid filter name request.
     */
    private function getFilterFactory($name)
    {
        if (array_key_exists($name, $this->factories)) {
            return $this->factories[$name];
        }
        
        throw new InvalidConfigurationException(
            sprintf(
                "Invalid filter name provided in configuration. Got '%s', available: %s",
                $name,
                implode(', ', array_keys($this->factories))
            )
        );
    }
}
