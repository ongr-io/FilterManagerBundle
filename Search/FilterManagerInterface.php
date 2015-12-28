<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Search;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for entry search point.
 */
interface FilterManagerInterface
{
    /**
     * Handles search request.
     *
     * @param Request $request
     *
     * @return SearchResponse
     */
    public function handleRequest(Request $request);
}
