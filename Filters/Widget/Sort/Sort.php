<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget\Sort;

use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\DSL\Sort\Sort as EsSort;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filters\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\AbstractSingleRequestValueFilter;
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
            $search->addSort(
                new EsSort(
                    $this->choices[$state->getValue()]['field'],
                    $this->choices[$state->getValue()]['order'],
                    null,
                    $this->choices[$state->getValue()]['mode']
                )
            );
        } else {
            foreach ($this->choices as $choice) {
                if ($choice['default']) {
                    $search->addSort(new EsSort($choice['field'], $choice['order']));

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
            $viewChoice->setMode($choice['mode']);
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
}
