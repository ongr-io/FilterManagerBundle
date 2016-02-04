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

use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareInterface;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareTrait;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filter for filtering on exact value in specified field.
 */
class DocumentValue extends AbstractSingleValue implements RelationAwareInterface
{
    use RelationAwareTrait;

    /**
     * Field name of the document object.
     *
     * @var string
     */
    private $documentField;

    /**
     * @var string
     */
    protected $value;

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

    /**
     * Setter for field value.
     *
     * @param $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = new FilterState();
        $document = $request->get('document');

        if ($document) {
            $this->setValue($document->{$this->getDocumentField()});
            $state->setActive(true);
        }

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $search->addPostFilter(new TermQuery($this->getField(), $this->getValue()));
    }
}
