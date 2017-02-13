<?php

namespace ONGR\FilterManagerBundle\Filter\Widget\Dynamic;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\FilterAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\NestedAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

class MultiDynamicAggregate extends DynamicAggregate
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
        if ($state && $state->isActive()) {
            $search->addPostFilter($this->getFilterQuery($state->getValue()));
        }
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
        &$deepLevelAggregation,
        $terms,
        $aggName
    ) {
        $boolQuery = $this->getFilterQuery($terms);

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
        if ($data->getState()->isActive()) {
            $value = $data->getState()->getValue();

            if (isset($value[$activeName]) && in_array($key, $value[$activeName])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $terms
     *
     * @return BoolQuery
     */
    private function getFilterQuery($terms)
    {
        list($path, $field) = explode('>', $this->getDocumentField());
        $boolQuery = new BoolQuery();

        foreach ($terms as $groupName => $values) {
            $innerBoolQuery = new BoolQuery();

            foreach ($values as $value) {
                $nestedBoolQuery = new BoolQuery();
                $nestedBoolQuery->add(new TermQuery($field, $value));
                $nestedBoolQuery->add(new TermQuery($this->getNameField(), $groupName));
                $innerBoolQuery->add(
                    new NestedQuery(
                        $path,
                        $nestedBoolQuery
                    ),
                    BoolQuery::SHOULD
                );
            }

            $boolQuery->add($innerBoolQuery);
        }

        return $boolQuery;
    }
}
