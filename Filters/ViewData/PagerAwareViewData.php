<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\ViewData;

use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Pager\PagerService;

/**
 * This class represents view data with choices.
 */
class PagerAwareViewData extends ViewData
{
    /**
     * @var PagerService
     */
    private $pager;

    /**
     * @param PagerService $pager
     */
    public function setPager($pager)
    {
        $this->pager = $pager;
    }

    /**
     * @return PagerService
     */
    public function getPager()
    {
        return $this->pager;
    }
}
