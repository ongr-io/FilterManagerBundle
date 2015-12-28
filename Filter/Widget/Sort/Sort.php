<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Sort;

use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\Widget\AbstractSingleRequestValueFilter;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * This class provides sorting filter.
 */
class Sort extends AbstractSingleRequestValueFilter implements ViewDataFactoryInterface
{
    /**
     * @var array
     */
    private $choices;

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $stateValue = $state->getValue();

            if (!empty($this->choices[$stateValue]['fields'])) {
                foreach ($this->choices[$stateValue]['fields'] as $sortField) {
                    $this->addFieldToSort($search, $sortField);
                }
            } else {
                $this->addFieldToSort($search, $this->choices[$stateValue]);
            }
        } else {
            foreach ($this->choices as $choice) {
                if ($choice['default']) {
                    $this->addFieldToSort($search, $choice);

                    break;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        // Nothing to do here.
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        return new ChoicesAwareViewData();
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        /** @var ChoicesAwareViewData $data */
        foreach ($this->choices as $key => $choice) {
            $active = $data->getState()->isActive() && strcmp($data->getState()->getValue(), $key) === 0;
            $viewChoice = new ViewData\Choice();
            $viewChoice->setLabel($choice['label']);
            $viewChoice->setDefault($choice['default']);
            if (isset($choice['mode'])) {
                $viewChoice->setMode($choice['mode']);
            }
            $viewChoice->setActive($active);
            if ($active) {
                $viewChoice->setUrlParameters($data->getResetUrlParameters());
            } else {
                $viewChoice->setUrlParameters($this->getOptionUrlParameters($key, $data));
            }
            $data->addChoice($viewChoice);
        }

        return $data;
    }

    /**
     * Sets possible choices list.
     *
     * @param array $choices
     */
    public function setChoices($choices)
    {
        foreach ($choices as $key => $choice) {
            $this->choices[isset($choice['key']) ? $choice['key'] : $key] = $choice;
        }
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
        $parameters[$this->getRequestField()] = $key;

        return $parameters;
    }

    /**
     * Adds sort field parameters into given search object.
     *
     * @param Search $search
     * @param array  $sortField
     */
    private function addFieldToSort(Search $search, $sortField)
    {
        $search->addSort(
            new FieldSort(
                $sortField['field'],
                $sortField['order'],
                isset($sortField['mode']) ? ['mode' => $sortField['mode']] : []
            )
        );
    }
}
