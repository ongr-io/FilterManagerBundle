<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Pager\Adapters;

use ONGR\FilterManagerBundle\Pager\PagerAdapterInterface;

/**
 * Adapter used to simply calculate offset for a given page.
 *
 * @deprecated Everything is moved to PagerAwareData
 */
class CountAdapter implements PagerAdapterInterface
{
    /**
     * @var int
     */
    private $count;

    /**
     * Constructor.
     *
     * @param int $count
     */
    public function __construct($count)
    {
        $this->count = $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalResults()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults($offset, $limit)
    {
        return [];
    }
}
