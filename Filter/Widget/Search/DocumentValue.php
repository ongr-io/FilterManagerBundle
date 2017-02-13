<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Search;

use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareTrait;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filter for filtering on exact value in specified field.
 */
class DocumentValue extends AbstractSingleValue
{

    use RelationAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = new FilterState();
        $document = $request->get('document');

        if (is_object($document)) {
            try {
                $closure = \Closure::bind(function ($document, $field) {
                    return $document->$field;
                }, null, $document);
                $state->setValue($closure($document, $this->getOption('field')));
                $state->setActive(true);
            } catch (\Exception $e) {
                throw new \LogicException("Cannot access document field.");
            }
        }

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $search->addPostFilter(new TermQuery($this->getDocumentField(), $state->getValue()));
    }
}
