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

use ONGR\FilterManagerBundle\Filter\ViewData;

/**
 * This interface allows user to provide custom instance of view data object.
 */
interface ViewDataFactoryInterface
{
    /**
     * Creates instance of specific ViewData.
     *
     * @return ViewData
     */
    public function createViewData();
}
