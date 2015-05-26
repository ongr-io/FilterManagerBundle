<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Widget\Choice;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\ViewData\ChoicesAwareViewData;
use ONGR\FilterManagerBundle\Filters\Widget\Choice\MultiTermChoice;
use ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice;
use ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
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

        // Case #0 should return only three items.
        $out[] = [new Request(['choice' => ['red', 'green']]), ['1', '3', '5']];

        // Case #1 no elements.
        $out[] = [new Request(['choice' => ['yellow', 'black']]), []];

        // Case #2 all elements.
        $out[] = [new Request(['choice' => ['red', 'green', 'blue']]), ['1', '2', '3', '4', '5']];

        // Case #3 non ordered choices.
        $out[] = [new Request(['choice' => [0 => 'black', 2 => 'red']]), ['1', '3']];

        return $out;
    }

    /**
     * Returns filter manager.
     *
     * @return FiltersManager
     */
    protected function getFilterManager()
    {
        $container = new FiltersContainer();

        $filter = new MultiTermChoice();
        $filter->setRequestField('choice');
        $filter->setField('color');
        $container->set('choice', $filter);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
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
        $result = $this->getFilterManager()->execute($request);

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

        $this->assertEquals($expectedUrlParams, $actualUrls);
    }
}
