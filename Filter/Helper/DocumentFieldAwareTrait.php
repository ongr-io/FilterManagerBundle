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
 *
 * @deprecated FieldAwareTrait will be changed to DocumentFieldAwareTrait in 2.0
 */
trait DocumentFieldAwareTrait
{
    /**
     * @var string
     */
    private $documentField;

    /**
     * @return string
     */
    public function getDocumentField()
    {
        return $this->documentField;
    }

    /**
     * @param string $documentField
     */
    public function setDocumentField($documentField)
    {
        $this->documentField = $documentField;
    }
}
