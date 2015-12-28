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
 * This trait provides properties for standard filter relations.
 */
trait RelationAwareTrait
{
    /**
     * @var RelationInterface|null
     */
    private $searchRelation;

    /**
     * @var RelationInterface|null
     */
    private $resetRelation;

    /**
     * @return null|RelationInterface
     */
    public function getResetRelation()
    {
        return $this->resetRelation;
    }

    /**
     * @param null|RelationInterface $resetRelation
     */
    public function setResetRelation($resetRelation)
    {
        $this->resetRelation = $resetRelation;
    }

    /**
     * @return null|RelationInterface
     */
    public function getSearchRelation()
    {
        return $this->searchRelation;
    }

    /**
     * @param null|RelationInterface $searchRelation
     */
    public function setSearchRelation($searchRelation)
    {
        $this->searchRelation = $searchRelation;
    }
}
