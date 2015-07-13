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
 * This class holds extra data for range and date_range filters.
 */
class RangeAwareViewData extends ViewData
{
    /**
     * @var float|\DateTime
     */
    private $minBounds;

    /**
     * @var float|\DateTime
     */
    private $maxBounds;

    /**
     * @return float|\DateTime
     */
    public function getMaxBounds()
    {
        return $this->maxBounds;
    }

    /**
     * @param float|\DateTime $maxBounds
     */
    public function setMaxBounds($maxBounds)
    {
        $this->maxBounds = $maxBounds;
    }

    /**
     * @return float|\DateTime
     */
    public function getMinBounds()
    {
        return $this->minBounds;
    }

    /**
     * @param float|\DateTime $minBounds
     */
    public function setMinBounds($minBounds)
    {
        $this->minBounds = $minBounds;
    }
}
