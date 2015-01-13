<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Choice;

use ONGR\ElasticsearchBundle\DSL\Filter\TermsFilter;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\Result\Aggregation\ValueAggregation;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class provides multiple terms choice filter.
 */
class MultiTermChoice extends SingleTermChoice
{
    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $search->addPostFilter(new TermsFilter($this->getField(), $state->getValue()));
        }
    }

    /**
     * Returns url with selected term applied.
     *
     * @param string   $key
     * @param ViewData $data
     *
     * @return array
     */
    protected function getOptionUrlParameters($key, ViewData $data)
    {
        $parameters = $data->getUrlParameters();

        if (isset($parameters[$this->getRequestField()])) {
            $parameters[$this->getRequestField()][] = $key;

        } else {
            $parameters[$this->getRequestField()] = [$key];
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUnsetUrlParameters($key, ViewData $data)
    {
        $parameters = $data->getUrlParameters();

        if (isset($parameters[$this->getRequestField()]) && count($parameters[$this->getRequestField()]) > 1) {
            $parameters[$this->getRequestField()] = array_values(
                array_diff($parameters[$this->getRequestField()], [$key])
            );
        } else {
            $parameters = $data->getResetUrlParameters();
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    protected function isChoiceActive($key, ViewData $data)
    {
        return $data->getState()->isActive() && in_array($key, $data->getState()->getValue());
    }
}
