<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Dynamic;

use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use ONGR\ElasticsearchDSL\Aggregation\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\NestedAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\SizeAwareTrait;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\ViewData\AggregateViewData;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Filter\Helper\FieldAwareInterface;
use ONGR\FilterManagerBundle\Filter\Helper\FieldAwareTrait;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class provides single terms choice.
 */
class DynamicAggregateFilter extends AbstractSingleRequestValueFilter implements FieldAwareInterface, ViewDataFactoryInterface
{
    use FieldAwareTrait, SizeAwareTrait;

    /**
     * @var array
     */
    private $sortType;

    /**
     * @var string
     */
    private $nameField;

    /**
     * @param array $sortType
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
    }

    /**
     * @return array
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * @return string
     */
    public function getNameField()
    {
        return $this->nameField;
    }

    /**
     * @param string $nameField
     */
    public function setNameField($nameField)
    {
        $this->nameField = $nameField;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        list($path, $field) = explode('>', $this->getField());

        if ($state && $state->isActive()) {
            $boolQuery = new BoolQuery();
            foreach ($state->getValue() as $value) {
                $boolQuery->add(
                    new NestedQuery(
                        $path,
                        new TermQuery($field, $value)
                    )
                );
            }
            $search->addPostFilter($boolQuery);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        list($path, $field) = explode('>', $this->getField());
        $name = $state->getName();
        $aggregation = new NestedAggregation(
            $name,
            $path
        );
        $termsAggregation = new TermsAggregation('query', $field);
        $termsAggregation->addParameter('size', 0);

        if ($this->getSortType()) {
            $termsAggregation->addParameter('order', [$this->getSortType()['type'] => $this->getSortType()['order']]);
        }

        if ($this->getSize() > 0) {
            $termsAggregation->addParameter('size', $this->getSize());
        }

        $termsAggregation->addAggregation(
            new TermsAggregation('name', $this->getNameField())
        );
        $aggregation->addAggregation($termsAggregation);
        $filterAggregation = new FilterAggregation($name . '-filter');
        $filterAggregation->setFilter($relatedSearch->getPostFilters());
        $filterAggregation->addAggregation($aggregation);

        $search->addAggregation($filterAggregation);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new AggregateViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        $unsortedChoices = [];

        /** @var AggregationValue $bucket */
        foreach ($this->fetchAggregation($result, $data->getName()) as $bucket) {
            $active = $this->isChoiceActive($bucket['key'], $data);
            $choice = new ViewData\Choice();
            $choice->setLabel($bucket->getValue('key'));
            $choice->setCount($bucket['doc_count']);
            $choice->setActive($active);
            if ($active) {
                $choice->setUrlParameters($this->getUnsetUrlParameters($bucket['key'], $data));
            } else {
                $choice->setUrlParameters($this->getOptionUrlParameters($bucket['key'], $data));
            }
            $unsortedChoices[$bucket->getAggregation('name')->getBuckets()[0]['key']][] = $choice;
        }

        /** @var AggregateViewData $data */
        foreach ($unsortedChoices as $name => $choices) {
            $choiceViewData = new ViewData\ChoicesAwareViewData();
            $choiceViewData->setName($name);
            $choiceViewData->setChoices($choices);
            $data->addItem($choiceViewData);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isRelated()
    {
        return true;
    }

    /**
     * Fetches buckets from search results.
     *
     * @param DocumentIterator $result Search results.
     * @param string           $name   Filter name.
     *
     * @return array Buckets.
     */
    private function fetchAggregation(DocumentIterator $result, $name)
    {
        $aggregation = $result->getAggregation($name);

        if (!$aggregation) {
            $aggregation = $result->getAggregation(sprintf('%s-filter', $name));
            $aggregation = $aggregation->getAggregation($name);
        }

        if ($aggregation) {
            return $aggregation->getAggregation('query');
        }

        return [];
    }

    /**
     * @param array  $values
     * @param string $currentValue
     *
     * @return BuilderInterface
     */
    private function createFilterQuery($values, $currentValue)
    {
        unset($values[array_search($currentValue, $values)]);

        if (empty($values)) {
            return new MatchAllQuery();
        }

        list($path, $field) = explode('>', $this->getField());
        $boolQuery = new BoolQuery();

        foreach ($values as $value) {
            $boolQuery->add(new TermQuery($field, $value));
        }

        return new NestedQuery($path, $boolQuery);
    }

    /**
     * @param string   $key
     * @param ViewData $data
     *
     * @return array
     */
    protected function getOptionUrlParameters($key, ViewData $data)
    {
        $parameters = $data->getResetUrlParameters();
        $parameters[$this->getRequestField()][] = $key;

        return $parameters;
    }

    /**
     * Returns url with selected term disabled.
     *
     * @param string   $key
     * @param ViewData $data
     *
     * @return array
     */
    private function getUnsetUrlParameters($key, ViewData $data)
    {
        return [];//$data->getResetUrlParameters();
    }

    /**
     * Returns whether choice with the specified key is active.
     *
     * @param string   $key
     * @param ViewData $data
     *
     * @return bool
     */
    protected function isChoiceActive($key, ViewData $data)
    {
        return $data->getState()->isActive() && in_array($key, $data->getState()->getValue());
    }
}
