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
 * This interface defines methods required to serialize object which will be
 * later JSON encoded.
 */
interface SerializableInterface
{
    /**
     * Returns all serializable data as scalar or array of scalars.
     *
     * @return mixed Serializable data
     */
    public function getSerializableData();
}
