<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Relations\LogicalJoin;

use ONGR\FilterManagerBundle\Relations\RelationInterface;

/**
 * This class joins several relations using "and" logical operator.
 */
class AndRelation implements RelationInterface
{
    /**
     * @var RelationInterface[]
     */
    private $relations;

    /**
     * @param RelationInterface[] $relations
     */
    public function __construct($relations = [])
    {
        $this->relations = $relations;
    }

    /**
     * {@inheritdoc}
     */
    public function isRelated($name)
    {
        foreach ($this->relations as $relation) {
            if (isset($relation) && $relation->isRelated($name) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param RelationInterface $relation
     */
    public function addRelation(RelationInterface $relation = null)
    {
        $this->relations[] = $relation;
    }
}
