<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Relation;

use ONGR\FilterManagerBundle\Relation\RelationInterface;

/**
 * This interface defines types of relations filter may have.
 */
interface RelationAwareInterface
{
    /**
     * Returns relation to other filters in terms of search conditions.
     *
     * @return RelationInterface
     */
    public function getSearchRelation();

    /**
     * Returns relation to other filters in terms of filter reset url parameters.
     *
     * @return RelationInterface
     */
    public function getResetRelation();
}
