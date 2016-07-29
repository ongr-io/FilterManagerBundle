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
use ONGR\ElasticsearchBundle\Mapping\Caser;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterInterface;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Relation\RelationAwareTrait;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class provides sorting filter.
 */
class Dynamic implements FilterInterface
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
     * @var array
     */
    private $filterNamespaces;

    /**
     * @var array $parameters
     */
    private $parameters;

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

        if (isset($value) && is_array($value)) {
            $filter = new $this->filterNamespaces[$value['filter']];
            $filter->setRequestField($this->requestField);
            $filter->setField($value['field']);
            if (isset($this->getParameters()[$value['filter']])) {
                foreach ($this->getParameters() as $key => $parameter) {
                    $setter = 'set'.ucfirst(Caser::camel($key));
                    $setter = $setter == 'setSort' ? 'setSortType' : $setter;
                    try {
                        $filter->$setter($parameter);
                    } catch (\Exception $e) {
                        throw new InvalidConfigurationException(
                            sprintf(
                                'Invalid parameter %s provided to Dynamic filters %s configuration.',
                                [$key,$value['filter']]
                            )
                        );
                    }
                }
            }
            $this->filter = $filter;
            $request = new Request(
                [$this->getRequestField() => $value['value']]
            );
            $state = $this->filter->getState($request);
        }

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $this->filter->modifySearch($search, $state, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        return $this->filter->preProcessSearch($search, $relatedSearch, $state);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        return $this->getViewData($result, $data);
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
     * @return array
     */
    public function getFilterNamespaces()
    {
        return $this->filterNamespaces;
    }

    /**
     * @param array $filterNamespaces
     */
    public function setFilterNamespaces($filterNamespaces)
    {
        $this->filterNamespaces = $filterNamespaces;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
