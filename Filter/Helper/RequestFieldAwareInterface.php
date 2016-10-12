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
 * This interface define structure for Elasticsearch field aware filters.
 */
interface RequestFieldAwareInterface
{
    /**
     * @param string $requestField
     */
    public function setRequestField($requestField);

    /**
     * @return string
     */
    public function getRequestField();
}
