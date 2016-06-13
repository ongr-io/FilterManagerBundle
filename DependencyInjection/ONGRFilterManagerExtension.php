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
        !isset($config['managers']) ? : $this->addFilterManagers($config, $container);
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
     * @param string $name Filter name.
     *
     * @return string
     */
    public static function getFilterId($name)
    {
        return sprintf('ongr_filter_manager.filter.%s', $name);
    }

    /**
     * Formats filter manager service id from given name.
     *
     * @param string $name Filter manager name.
     *
     * @return string
     */
    public static function getFilterManagerId($name)
    {
        return sprintf('ongr_filter_manager.%s', $name);
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

                $container->setDefinition(self::getFilterId($name), $filterDefinition);
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
    private function addFilterManagers(array $config, ContainerBuilder $container)
    {
        foreach ($config['managers'] as $name => $manager) {
            $filterContainer = new Definition('ONGR\FilterManagerBundle\Search\FilterContainer');
            $cacheEngine = $config['cache']['engine'] ? new Reference($config['cache']['engine']) : null;
            
            $filterContainer
                ->addMethodCall('setCache', [$cacheEngine])
                ->addMethodCall('setExclude', [$config['cache']['exclude']])
                ->addMethodCall('setLifeTime', [$config['cache']['life_time']]);

            foreach ($manager['filters'] as $filter) {
                $filterContainer->addMethodCall(
                    'set',
                    [$filter, new Reference(self::getFilterId($filter))]
                );
            }

            $managerDefinition = new Definition(
                'ONGR\FilterManagerBundle\Search\FilterManager',
                [
                    $filterContainer,
                    new Reference($manager['repository']),
                    new Reference('event_dispatcher'),
                ]
            );
            $managerDefinition->addTag('es.filter_manager');

            $container->setDefinition(self::getFilterManagerId($name), $managerDefinition);
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
            sprintf('ONGR\FilterManagerBundle\Relation\%sRelation', ucfirst($type)),
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
