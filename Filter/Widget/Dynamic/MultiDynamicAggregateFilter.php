<?php

namespace ONGR\FilterManagerBundle\Filter\Widget\Dynamic;

use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchDSL\Aggregation\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\NestedAggregation;
use ONGR\ElasticsearchDSL\Aggregation\TermsAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermsQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

class MultiDynamicAggregateFilter extends DynamicAggregateFilter
{
    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = new FilterState();
        $value = $request->get($this->getRequestField());

        if (isset($value) && is_array($value) && reset($value) && is_array(reset($value))) {
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

            foreach ($state->getValue() as $groupName => $values) {
                $innerBoolQuery = new BoolQuery();

                foreach ($values as $value) {
                    $innerBoolQuery->add(
                        new NestedQuery(
                            $path,
                            new TermQuery($field, $value)
                        ),
                        BoolQuery::SHOULD
                    );
                }

                $boolQuery->add($innerBoolQuery);
            }

            $search->addPostFilter($boolQuery);
        }
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
        $aggregation = $result->getAggregation(sprintf('%s-filter', $filterName));

        if ($aggregation->getAggregation($filterName)) {
            $aggregation = $aggregation->find($filterName.'.query');
            $data['all-selected'] = $aggregation;

            return $data;
        }

        if (!empty($values)) {
            foreach ($values as $name => $value) {
                $data[$name] = $aggregation->find(sprintf('%s.%s.query', $name, $filterName));
            }

            $data['all-selected'] = $aggregation->find(sprintf('all-selected.%s.query', $filterName));

            return $data;
        }

        return [];
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

        foreach ($terms as $namedTerms) {
            $boolQuery->add(
                new NestedQuery($path, new TermsQuery($field, array_values($namedTerms)))
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
                unset($value[$name][array_search($key, $value[$name])]);
                $parameters[$this->getRequestField()] = $value;

                return $parameters;
            }

            $parameters[$this->getRequestField()] = $value;
        }

        $parameters[$this->getRequestField()][$name][] = $key;

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
        return $data->getState()->isActive() &&
            isset($data->getState()->getValue()[$activeName]) &&
            in_array($key, $data->getState()->getValue()[$activeName]);
    }
}
