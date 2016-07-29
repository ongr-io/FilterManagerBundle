<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\DependencyInjection\Filter;

use Symfony\Component\DependencyInjection\Definition;

/**
 * Factory for dynamic filter.
 */
class DynamicFilterFactory extends AbstractFilterFactory
{
    /**
     * @var array
     */
    private $filterNamespaces;

    /**
     * {@inheritdoc}
     */
    public function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);
        $definition->addMethodCall('setParameters', [$configuration['parameters']]);
        $definition->addMethodCall('setFilterNamespaces', [$configuration['filter_namespaces']]);
    }

    /**
     * Sets configuraton for filter.
     *
     * @param array $filterNamespaces
     *
     * @return AbstractFilterFactory
     */
    public function setFilterNamespaces(array $filterNamespaces)
    {
        $this->filterNamespaces = $filterNamespaces;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filters\Widget\Dynamic\Dynamic';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dynamic';
    }
}
