<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\Widget\Range;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\ViewData\RangeAwareViewData;
use Symfony\Component\HttpFoundation\Request;

/**
 * Date range filter, selects documents from lower date to upper date.
 */
class DateRange extends Range
{
    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = AbstractRange::getState($request);

        if ($state->getValue()) {
            $values = explode(';', $state->getValue(), 2);

            if (count($values) < 2) {
                $state->setActive(false);

                return $state;
            }

            $gt = $this->isInclusive() ? 'gte' : 'gt';
            $lt = $this->isInclusive() ? 'lte' : 'lt';

            $normalized[$gt] = $values[0];
            $normalized[$lt] = $values[1];

            $state->setValue($normalized);
        }

        return $state;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(DocumentIterator $result, ViewData $data)
    {
        $name = $data->getState()->getName();

        if (!$agg = $result->getAggregation($name)) {
            $agg = $result->getAggregation($name . '-filter')->getAggregation($name);
        }
        /** @var $data RangeAwareViewData */
        $data->setMinBounds(
            new \DateTime('@' . (int) ($agg['min'] / 1000))
        );

        $data->setMaxBounds(
            new \DateTime('@' . (int) ($agg['max'] / 1000))
        );

        return $data;
    }
}
