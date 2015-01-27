<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Pager;

/**
 * Interface PagerAdapterInterface.
 *
 * @package ONGR\FilterManagerBundle\Pager
 */
interface PagerAdapterInterface
{
    /**
     * Returns the list of results.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getResults($offset, $limit);

    /**
     * Returns the total number of results.
     *
     * @return int
     */
    public function getTotalResults();
}
