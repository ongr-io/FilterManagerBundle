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
 * Factory for choice filter.
 */
class DynamicAggregateFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        $definition->addMethodCall('setField', [$configuration['field']]);
        $definition->addMethodCall('setNameField', [$configuration['name_field']]);
        $definition->addMethodCall('setShowZeroChoices', [$configuration['show_zero_choices']]);

        if (isset($configuration['sort']) && count($configuration['sort']) > 0) {
            $definition->addMethodCall('setSortType', [$configuration['sort']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Dynamic\DynamicAggregate';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dynamic_aggregate';
    }
}
