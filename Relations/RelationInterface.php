<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Relations;

/**
 * This interface defines methods to evaluate relations between filters.
 */
interface RelationInterface
{
    /**
     * Return true if object is related with given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isRelated($name);
}
