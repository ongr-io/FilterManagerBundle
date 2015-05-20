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
 * Factory for range filter on date fields.
 */
class DateRangeFilterFactory extends RangeFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filters\Widget\Range\DateRange';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date_range';
    }
}
