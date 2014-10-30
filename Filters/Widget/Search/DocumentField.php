<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Search;

use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class filters results by request document's field value
 */
class DocumentField extends MatchSearch
{
    /**
     * {@inheritdoc}
     */
    public function getField()
    {
        $field = parent::getField();

        if ($field === null) {
            throw new \InvalidArgumentException('Field must be set.');
        }

        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = parent::getState($request);

        $value = $this->extractDocumentValue($request);

        if ($value !== null) {
            $state->setValue($value);
            $state->setActive(true);
        }

        return $state;
    }

    /**
     * Extracts document value
     *
     * @param Request $request
     *
     * @return mixed
     */
    protected function extractDocumentValue(Request $request)
    {
        $document = $request->get($this->getRequestField());

        if (!$document instanceof DocumentInterface) {
            return null;
        }

        return $document->getId();
    }
}
