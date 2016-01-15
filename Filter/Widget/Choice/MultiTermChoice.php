<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Choice;

use ONGR\ElasticsearchDSL\Filter\NotFilter;
use ONGR\ElasticsearchDSL\Filter\TermsFilter;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class provides multiple terms choice filter.
 */
class MultiTermChoice extends SingleTermChoice
{

    const OPERATION_OR = 'or';
    const OPERATION_AND = 'and';
    const OPERATION_NOT_AND = 'not_and';

    /**
     * @var string
     */
    private $booleanOperation;

    /**
     * @return mixed
     */
    public function getBooleanOperation()
    {
        return $this->booleanOperation;
    }

    /**
     * @param $operation
     */
    public function setBooleanOperation($operation)
    {
        $this->booleanOperation = $operation;
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        if ($state && $state->isActive()) {
            $filter = new TermsFilter($this->getField(), $state->getValue());

            switch (strtolower($this->getBooleanOperation())) {
                case self::OPERATION_AND:
                    $filter->setParameters(['execution' => 'and']);
                    break;
                case self::OPERATION_NOT_AND:
                    $filter = new NotFilter($filter);
                    break;
                case self::OPERATION_OR:
                default:
                    // Do nothing
            }

            $search->addPostFilter($filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $value = $request->get($this->getRequestField());

        if (isset($value) && $value !== '' && !is_array($value)) {
            $request->query->set($this->getRequestField(), [ $value ]);
        }

        return parent::getState($request);
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
