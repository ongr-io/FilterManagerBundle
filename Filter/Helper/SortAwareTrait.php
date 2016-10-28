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
     * @var string $default
     * @return string
     */
    public function getSortType($default = '_count')
    {
        return $this->getOption('sort_type', $default);
    }

    /**
     * @var string $default
     * @return string
     */
    public function getSortOrder($default = 'asc')
    {
        return $this->getOption('sort_order', $default);
    }

    /**
     * @var array $default
     * @return array
     */
    public function getSortPriority($default = [])
    {
        return $this->getOption('sort_priority', $default);
    }
}
