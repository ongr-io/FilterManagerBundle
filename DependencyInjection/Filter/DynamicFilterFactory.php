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
use Symfony\Component\DependencyInjection\Reference;

/**
 * Factory for dynamic filter.
 */
class DynamicFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    public function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);
        $filters = [];

        foreach ($configuration['filters'] as $filter) {
            $filters[$filter] = new Reference('ongr_filter_manager.filter.'.$filter);
        }

        $definition->addMethodCall('setFilters', [$filters]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Dynamic\Dynamic';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dynamic';
    }
}
