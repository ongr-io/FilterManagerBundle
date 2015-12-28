<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Relation;

/**
 * This class represents "exclude" relation.
 */
class ExcludeRelation implements RelationInterface
{
    /**
     * @var string[]
     */
    private $names;

    /**
     * @param array $names
     */
    public function __construct(array $names = [])
    {
        $this->names = array_flip($names);
    }

    /**
     * {@inheritdoc}
     */
    public function isRelated($name)
    {
        return !isset($this->names[$name]);
    }
}
