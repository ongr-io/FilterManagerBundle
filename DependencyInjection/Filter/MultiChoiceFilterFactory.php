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
 * Factory for multi choice filter.
 */
class MultiChoiceFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        $definition->addMethodCall('setField', [$configuration['field']]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filters\Widget\Choice\MultiTermChoice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'multi_choice';
    }
}
