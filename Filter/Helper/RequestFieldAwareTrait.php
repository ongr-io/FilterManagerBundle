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
 * This trait defines methods for Elasticsearch field value aware filters.
 */
trait RequestFieldAwareTrait
{
    /**
     * @var string
     */
    private $requestField;

    /**
     * @return string
     */
    public function getRequestField()
    {
        return $this->requestField;
    }

    /**
     * @param string $requestField
     */
    public function setRequestField($requestField)
    {
        $this->requestField = $requestField;
    }
}
