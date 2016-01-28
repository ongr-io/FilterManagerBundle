<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Choice;

use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Choice\MultiTermChoice;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use ONGR\FilterManagerBundle\Test\AbstractFilterManagerResultsTest;
use Symfony\Component\HttpFoundation\Request;

class MultiTermChoiceTest extends AbstractFilterManagerResultsTest
{
    /**
     * @return array
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'color' => 'red',
                        'manufacturer' => 'a',
                    ],
                    [
                        '_id' => 2,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                    ],
                    [
                        '_id' => 3,
                        'color' => 'red',
                        'manufacturer' => 'b',
                    ],
                    [
                        '_id' => 4,
                        'color' => 'blue',
                        'manufacturer' => 'b',
                    ],
                    [
                        '_id' => 5,
                        'color' => 'green',
                        'manufacturer' => 'b',
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0
        $out[] = [new Request(['choice' => ['red', 'green']]), ['1', '3', '5']];

        // Case #1
        $out[] = [new Request(['choice' => ['yellow', 'black']]), []];

        // Case #2
        $out[] = [new Request(['choice' => ['red', 'green', 'blue']]), ['1', '2', '3', '4', '5']];

        // Case #3
        $out[] = [new Request(['choice' => [0 => 'black', 2 => 'red']]), ['1', '3']];

        // Case #4
        $out[] = [new Request(['choice' => 'red']), ['1', '3']];

        // Case #5
        $out[] = [new Request(['choice' => ['red', 'green']]), ['1', '3', '5']];

        return $out;
    }

    /**
     * Returns filter manager.
     *
     * @return FilterManager
     */
    protected function getFilterManager()
    {
        $container = new FilterContainer();

        $filter = new MultiTermChoice();
        $filter->setRequestField('choice');
        $filter->setTags(['badged']);
        $filter->setField('color');

        $container->set('choice', $filter);

        return new FilterManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * Data provider for testChoiceUrl.
     *
     * @return array
     */
    public function getChoiceUrlData()
    {
        // Case #0 simple data.
        $out[] = [
            new Request(['choice' => ['red', 'green']]),
            [
                [
                    'red',
                    'green',
                    'blue',
                ],
                [
                    'green',
                ],
                [
                    'red',
                ],
            ],
        ];

        // Case #1 Nothing selected.
        $out[] = [
            new Request(),
            [
                [
                    'blue',
                ],
                [
                    'red',
                ],
                [
                    'green',
                ],
            ],
        ];

        // Case #2 Only one selected.
        $out[] = [
            new Request(['choice' => ['red']]),
            [
                [
                    'red',
                    'blue',
                ],
                [],
                [
                    'red',
                    'green',
                ],
            ],
        ];

        return $out;
    }

    /**
     * Check if  urls returned is as expected in all cases.
     *
     * @param Request $request
     * @param array   $expectedUrlParams
     *
     * @dataProvider getChoiceUrlData()
     */
    public function testChoiceUrl(Request $request, array $expectedUrlParams)
    {
        $result = $this->getFilterManager()->handleRequest($request);

        /** @var ChoicesAwareViewData $viewData */
        $viewData = $result->getFilters()['choice'];
        $actualUrls = [];

        foreach ($viewData->getChoices() as $choice) {
            if (isset($choice->getUrlParameters()['choice'])) {
                $actualUrls[] = $choice->getUrlParameters()['choice'];
            } else {
                $actualUrls[] = [];
            }
        }

        $this->assertTrue($viewData->hasTag('badged'));
        $this->assertEquals($expectedUrlParams, $actualUrls);
    }

    /**
     * This method asserts if search request gives expected results.
     *
     * @param Request $request     Http request.
     * @param array   $ids         Array of document ids to assert.
     * @param bool    $assertOrder Set true if order of results lso should be asserted.
     *
     * @dataProvider getTestResultsData()
     */
    public function testResults(
        Request $request,
        $ids,
        $assertOrder = false
    ) {
        $actual = array_map(
            [$this, 'fetchDocumentId'],
            iterator_to_array($this->getFilterManager()->handleRequest($request)->getResult())
        );

        $this->assertEquals(sort($ids), sort($actual));
    }
}
