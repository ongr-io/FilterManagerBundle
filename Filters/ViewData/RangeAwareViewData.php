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

/**
 * This class holds extra data for range filter.
 */
class RangeAwareViewData extends ViewData
{
    /**
     * @var float
     */
    private $minBounds;

    /**
     * @var float
     */
    private $maxBounds;

    /**
     * @return float
     */
    public function getMaxBounds()
    {
        return $this->maxBounds;
    }

    /**
     * @param float $maxBounds
     */
    public function setMaxBounds($maxBounds)
    {
        $this->maxBounds = $maxBounds;
    }

    /**
     * @return float
     */
    public function getMinBounds()
    {
        return $this->minBounds;
    }

    /**
     * @param float $minBounds
     */
    public function setMinBounds($minBounds)
    {
        $this->minBounds = $minBounds;
    }
}
