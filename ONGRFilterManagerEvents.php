<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle;

/**
 * Contains all events thrown in the ONGRFilterManagerBundle
 */
final class ONGRFilterManagerEvents
{
    /**
     * The PRE_SEARCH event occurs before search process is initialized
     */
    const PRE_SEARCH = 'ongr_filter_manager.pre_search';

    /**
     * The SEARCH_RESPONSE event occurs after search is executed
     */
    const SEARCH_RESPONSE = 'ongr_filter_manager.search_response';
}
