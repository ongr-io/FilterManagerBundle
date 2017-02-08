<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Range;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for date range filter.
 */
class DateRangeTest extends AbstractElasticsearchTestCase
{
    /**
     * @return array
     */
    public function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'date' => '2001-09-11',
                    ],
                    [
                        '_id' => 2,
                        'date' => '2002-09-12',
                    ],
                    [
                        '_id' => 3,
                        'date' => '2003-09-11',
                    ],
                    [
                        '_id' => 4,
                        'date' => '2004-09-11',
                    ],
                    [
                        '_id' => 5,
                        'date' => '2005-10-11',
                    ],
                ],
            ],
        ];
    }


    /**
     * Data provider.
     *
     * @return array
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0
        $out[] = [
            [3,2],
            ['date_range' => '2002-09-11;2004-09-11'],
        ];

        // Case #1
        $out[] = [
            [3,2],
            ['date_range' => '1031788700000;1063239400000'],
        ];

        // Case #1
        $out[] = [
            [3,2],
            ['date_range' => '2002-09-11;1063239400000'],
        ];

        return $out;
    }

    /**
     * Check if choices are filtered and sorted as expected.
     *
     * @param array $expectedChoices
     * @param array $query
     *
     * @dataProvider getTestResultsData()
     */
    public function testFilter($expectedChoices, $query = [])
    {

        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('range'));
        $result = $manager->handleRequest(new Request($query))->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals($expectedChoices, $actual);
    }
}
