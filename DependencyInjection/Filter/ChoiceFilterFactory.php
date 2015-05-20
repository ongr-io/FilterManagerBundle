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
class ChoiceFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        $definition->addMethodCall('setField', [$configuration['field']]);
        
        if (isset($configuration['size'])) {
            $definition->addMethodCall('setSize', [$configuration['size']]);
        }

        if (isset($configuration['min_doc_count'])) {
            $definition->addMethodCall('setMinDocCount', [$configuration['min_doc_count']]);
        }
        
        if (isset($configuration['sort']) && count($configuration['sort']) > 0) {
            $definition->addMethodCall('setSortType', [$configuration['sort']]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'choice';
    }
}
