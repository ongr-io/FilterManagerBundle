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
 * Factory for pager filter.
 */
class PagerFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        $definition->addMethodCall('setCountPerPage', [$configuration['count_per_page']]);
        $definition->addMethodCall('setMaxPages', [$configuration['max_pages']]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filters\Widget\Pager\Pager';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pager';
    }
}
