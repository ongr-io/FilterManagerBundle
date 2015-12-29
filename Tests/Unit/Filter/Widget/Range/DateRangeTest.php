<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\Widget\Range;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Filter\ViewData\RangeAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Range\DateRange;

/**
 * Unit test for date_range filter.
 */
class DateRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This unit test checks if view data from raw aggregations are correctly transferred to ViewData objects.
     */
    public function testGetViewData()
    {
        $managerMock = $this->getMockBuilder('ONGR\ElasticsearchBundle\Service\Manager')
            ->disableOriginalConstructor()
            ->setMethods(['getConverter', 'getConfig'])
            ->getMock();

        $managerMock->expects($this->any())->method('getConverter')->willReturnSelf();
        $managerMock->expects($this->any())->method('getConfig')->willReturn([]);

        $dateRange = new DateRange();

        $minDate = new \DateTime('-1 week');
        $maxDate = new \DateTime();

        $iterator = new DocumentIterator(
            [
                'aggregations' => [
                    'agg_date_range_agg' => [
                        'min' => $minDate->getTimestamp() * 1000,
                        'max' => $maxDate->getTimestamp() * 1000,
                    ],
                ],
            ],
            $managerMock,
            []
        );

        $originalViewData = new RangeAwareViewData();

        $expectedViewData = new RangeAwareViewData();
        $expectedViewData->setMinBounds($minDate);
        $expectedViewData->setMaxBounds($maxDate);

        $this->assertEquals(
            $expectedViewData,
            $dateRange->getViewData($iterator, $originalViewData)
        );
    }
}
