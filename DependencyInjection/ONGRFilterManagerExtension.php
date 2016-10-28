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

    const PREFIX = 'ongr_filter_manager';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('ongr_filter_manager.filters', $config['filters']);
        $container->setParameter('ongr_filter_manager.managers', $config['managers']);
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
        return sprintf(self::PREFIX.'.filter.%s', $name);
    }

    /**
     * Formats filter manager service id from given name.
     *
     * @param string $name Filter manager name.
     *
     * @return string
     */
    public static function getFilterManagerId($name = 'default')
    {
        return sprintf(self::PREFIX.'.manager.%s', $name);
    }
}
