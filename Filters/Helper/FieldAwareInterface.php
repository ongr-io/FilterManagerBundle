<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Helper;

/**
 * This interface define structure for elasticsearch field aware filters.
 */
interface FieldAwareInterface
{
    /**
     * @param string $field
     */
    public function setField($field);

    /**
     * @return string
     */
    public function getField();
}
