<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\ViewData;

/**
 * This class represents view data with aggregated choices.
 */
class AggregateViewData extends ViewData
{
    /**
     * @var ChoicesAwareViewData[]
     */
    private $items;

    /**
     * @return ChoicesAwareViewData[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @param ChoicesAwareViewData $item
     */
    public function addItem(ChoicesAwareViewData $item)
    {
        $this->items[] = $item;
    }
}
