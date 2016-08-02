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

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterInterface;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Helper\ViewDataFactoryInterface;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareTrait;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * This class a dynamic filter that can change the filter, field and value per request
 */
class Dynamic implements FilterInterface, ViewDataFactoryInterface
{
    use RelationAwareTrait;

    /**
     * @var string
     */
    private $requestField;

    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var FilterInterface[]
     */
    private $filters;

    /**
     * @var array
     */
    private $urlParameters;

    /**
     * @var array
     */
    private $tags = [];

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = new FilterState();
        $value = $request->get($this->getRequestField());
        $this->urlParameters[$this->getRequestField()] = $value;

        if (isset($value) && is_array($value)) {
            if (!isset($value['field']) || !isset($value['filter'])) {
                throw new BadRequestHttpException(
                    '`field` and `filter` values must be provided to the dynamic filter'
                );
            }

            if (isset($this->filters[$value['filter']])) {
                $this->filter = clone $this->filters[$value['filter']];
            } else {
                throw new InvalidConfigurationException(
                    sprintf('Filter `%s`, requested in dynamic filter is not defined', $value['filter'])
                );
            }

            $this->filter->setRequestField($this->getRequestField());
            $this->filter->setField($value['field']);
            $requestValue = [];

            if (isset($value['value'])) {
                $requestValue = [
                    $this->getRequestField() => $this->urlParameters[$this->getRequestField()]['value']
                ];
            }

            $request = new Request($requestValue);
            $state = $this->filter->getState($request);
        }

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $this->filter->modifySearch($search, $state, $request);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        $out = null;

        if ($this->filter) {
            $out = $this->filter->preProcessSearch($search, $relatedSearch, $state);
            $state->setUrlParameters($this->urlParameters);
        }

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        if ($this->filter) {
            $data = $this->filter->getViewData($result, $data);

            if ($data instanceof ChoicesAwareViewData) {
                $choices = [];

                foreach ($data->getChoices() as $choice) {
                    $urlParameters = $this->urlParameters;
                    $choiceParameters = $choice->getUrlParameters();
                    $value = null;

                    if (isset($choiceParameters[$this->getRequestField()])) {
                        $value = $choiceParameters[$this->getRequestField()];
                    }

                    $urlParameters[$this->getRequestField()]['value'] = $value;
                    $choiceParameters[$this->getRequestField()] = $urlParameters[$this->getRequestField()];

                    $choice->setUrlParameters($choiceParameters);
                    $choices[] = $choice;
                }

                $data->setChoices($choices);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function createViewData()
    {
        if ($this->filter and $this->filter instanceof ViewDataFactoryInterface) {
            $data = $this->filter->createViewData();
        } else {
            $data = new ViewData();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getRequestField()
    {
        return $this->requestField;
    }

    /**
     * @param mixed $requestField
     */
    public function setRequestField($requestField)
    {
        $this->requestField = $requestField;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param FilterInterface[] $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }
}
