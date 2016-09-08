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

/**
 * Factory for choice filter.
 */
class MultiDynamicAggregateFilterFactory extends DynamicAggregateFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Dynamic\MultiDynamicAggregateFilter';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'multi_dynamic_aggregate';
    }
}
