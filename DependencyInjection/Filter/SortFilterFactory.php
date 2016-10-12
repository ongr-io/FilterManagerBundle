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
 * Factory for sort filter.
 *
 * @deprecated Filter factories will be deleted in 2.0
 */
class SortFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        $definition->addMethodCall('setChoices', [$configuration['choices']]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Sort\Sort';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sort';
    }
}
