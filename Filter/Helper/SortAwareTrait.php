<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Helper;

/**
 * This trait defines methods for choices sorting.
 */
trait SortAwareTrait
{
    use OptionsAwareTrait;

    /**
     * @return string
     */
    public function getSortType()
    {
        return $this->getOption('sort_type', '_count');
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        return $this->getOption('sort_order', 'asc');
    }

    /**
     * @return string
     */
    public function getSortPriority()
    {
        return $this->getOption('sort_priority', []);
    }
}
