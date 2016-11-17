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

/**
 * Date range filter, selects documents from lower date to upper date.
 */
class DateRange extends Range
{
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
