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
class DynamicAggregate extends AbstractSingleRequestValueFilter implements
    FieldAwareInterface,
    ViewDataFactoryInterface
{
    use FieldAwareTrait;

    /**
     * @var array
     */
    private $sortType;

    /**
     * @var string
     */
    private $nameField;

    /**
     * @var bool
     */
    private $showZeroChoices;

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
     * @return bool
     */
    public function getShowZeroChoices()
    {
        return $this->showZeroChoices;
    }

    /**
     * @param bool $showZeroChoices
     */
    public function setShowZeroChoices($showZeroChoices)
    {
        $this->showZeroChoices = $showZeroChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = new FilterState();
        $value = $request->get($this->getRequestField());

        if (isset($value) && is_array($value)) {
            $state->setActive(true);
            $state->setValue($value);
            $state->setUrlParameters([$this->getRequestField() => $value]);
        }

        return $state;
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

        $termsAggregation->addAggregation(
            new TermsAggregation('name', $this->getNameField())
        );
        $aggregation->addAggregation($termsAggregation);
        $filterAggregation = new FilterAggregation($name . '-filter');

        if (!empty($relatedSearch->getPostFilters())) {
            $filterAggregation->setFilter($relatedSearch->getPostFilters());
        } else {
            $filterAggregation->setFilter(new MatchAllQuery());
        }

        if ($state->isActive()) {
            foreach ($state->getValue() as $key => $term) {
                $terms = $state->getValue();
                unset($terms[$key]);

                $this->addSubFilterAggregation(
                    $filterAggregation,
                    $aggregation,
                    $terms,
                    $key
                );
            }
        }

        $this->addSubFilterAggregation(
            $filterAggregation,
            $aggregation,
            $state->getValue() ? $state->getValue() : [],
            'all-selected'
        );

        $search->addAggregation($filterAggregation);

        if ($this->getShowZeroChoices()) {
            $search->addAggregation($aggregation);
        }
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
        $activeNames = $data->getState()->isActive() ? array_keys($data->getState()->getValue()) : [];
        $filterAggregations = $this->fetchAggregation($result, $data->getName(), $data->getState()->getValue());

        if ($this->getShowZeroChoices() && $data->getState()->isActive()) {
            $unsortedChoices = $this->formInitialUnsortedChoices($result, $data);
        }

        /** @var AggregationValue $bucket */
        foreach ($filterAggregations as $activeName => $aggregation) {
            foreach ($aggregation as $bucket) {
                $name = $bucket->getAggregation('name')->getBuckets()[0]['key'];

                if ($name != $activeName && $activeName != 'all-selected') {
                    continue;
                }

                $active = $this->isChoiceActive($bucket['key'], $data, $activeName);
                $choice = new ViewData\Choice();
                $choice->setLabel($bucket->getValue('key'));
                $choice->setCount($bucket['doc_count']);
                $choice->setActive($active);

                $choice->setUrlParameters(
                    $this->getOptionUrlParameters($bucket['key'], $name, $data, $active)
                );

                if ($activeName == 'all-selected') {
                    $unsortedChoices[$activeName][$name][$bucket['key']] = $choice;
                } else {
                    $unsortedChoices[$activeName][$bucket['key']] = $choice;
                }
            }
        }

        if (isset($unsortedChoices['all-selected'])) {
            foreach ($unsortedChoices['all-selected'] as $name => $buckets) {
                if (in_array($name, $activeNames)) {
                    continue;
                }

                $unsortedChoices[$name] = $buckets;
            }

            unset($unsortedChoices['all-selected']);
        }

        ksort($unsortedChoices);

        /** @var AggregateViewData $data */
        foreach ($unsortedChoices as $name => $choices) {
            $choiceViewData = new ViewData\ChoicesAwareViewData();
            $choiceViewData->setName($name);
            $choiceViewData->setChoices($choices);
            $choiceViewData->setUrlParameters([]);
            $choiceViewData->setResetUrlParameters([]);
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
     * @param DocumentIterator $result     Search results.
     * @param string           $filterName Filter name.
     * @param array            $values     Values from the state object
     *
     * @return array Buckets.
     */
    protected function fetchAggregation(DocumentIterator $result, $filterName, $values)
    {
        $data = [];
        $values = empty($values) ? [] : $values;
        $aggregation = $result->getAggregation(sprintf('%s-filter', $filterName));

        foreach ($values as $name => $value) {
            $data[$name] = $aggregation->find(sprintf('%s.%s.query', $name, $filterName));
        }

        $data['all-selected'] = $aggregation->find(sprintf('all-selected.%s.query', $filterName));

        return $data;
    }

    /**
     * A method used to add an additional filter to the aggregations
     * in preProcessSearch
     *
     * @param FilterAggregation $filterAggregation
     * @param NestedAggregation $deepLevelAggregation
     * @param array             $terms Terms of additional filter
     * @param string            $aggName
     *
     * @return BuilderInterface
     */
    protected function addSubFilterAggregation(
        $filterAggregation,
        $deepLevelAggregation,
        $terms,
        $aggName
    ) {
        list($path, $field) = explode('>', $this->getField());
        $boolQuery = new BoolQuery();

        foreach ($terms as $term) {
            $boolQuery->add(
                new NestedQuery($path, new TermQuery($field, $term))
            );
        }

        if ($boolQuery->getQueries() == []) {
            $boolQuery->add(new MatchAllQuery());
        }

        $innerFilterAggregation = new FilterAggregation(
            $aggName,
            $boolQuery
        );
        $innerFilterAggregation->addAggregation($deepLevelAggregation);
        $filterAggregation->addAggregation($innerFilterAggregation);
    }

    /**
     * @param string   $key
     * @param string   $name
     * @param ViewData $data
     * @param bool     $active True when the choice is active
     *
     * @return array
     */
    protected function getOptionUrlParameters($key, $name, ViewData $data, $active)
    {
        $value = $data->getState()->getValue();
        $parameters = $data->getResetUrlParameters();

        if (!empty($value)) {
            if ($active) {
                unset($value[array_search($key, $value)]);
                $parameters[$this->getRequestField()] = $value;

                return $parameters;
            }

            $parameters[$this->getRequestField()] = $value;
        }

        $parameters[$this->getRequestField()][$name] = $key;

        return $parameters;
    }

    /**
     * Returns whether choice with the specified key is active.
     *
     * @param string   $key
     * @param ViewData $data
     * @param string   $activeName
     *
     * @return bool
     */
    protected function isChoiceActive($key, ViewData $data, $activeName)
    {
        return $data->getState()->isActive() && in_array($key, $data->getState()->getValue());
    }

    /**
     * Forms $unsortedChoices array with all possible choices.
     * 0 is assigned to the document count of the choices.
     *
     * @param DocumentIterator $result
     * @param ViewData         $data
     *
     * @return array
     */
    private function formInitialUnsortedChoices($result, $data)
    {
        $unsortedChoices = [];
        $urlParameters = array_merge(
            $data->getResetUrlParameters(),
            $data->getState()->getUrlParameters()
        );

        foreach ($result->getAggregation($data->getName())->getAggregation('query') as $bucket) {
            $groupName = $bucket->getAggregation('name')->getBuckets()[0]['key'];
            $choice = new ViewData\Choice();
            $choice->setActive(false);
            $choice->setUrlParameters($urlParameters);
            $choice->setLabel($bucket['key']);
            $choice->setCount(0);
            $unsortedChoices[$groupName][$bucket['key']] = $choice;
            $unsortedChoices['all-selected'][$groupName][$bucket['key']] = $choice;
        }

        return $unsortedChoices;
    }
}
