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
use ONGR\FilterManagerBundle\Filters\Widget\Choice\SingleTermChoice;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class SingleTermChoiceTest extends ElasticsearchTestCase
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
                    [
                        '_id' => 6,
                        'color' => 'blue',
                        'manufacturer' => 'a',
                    ],
                    [
                        '_id' => 7,
                        'color' => 'yellow',
                        'manufacturer' => 'a',
                    ],
                ],
            ],
        ];
    }

    /**
     * Check if choices are sorted as expected using configuration settings.
     */
    public function testChoicesConfiguration()
    {
        /** @var ChoicesAwareViewData $result */
        $result = $this->getContainer()->get('ongr_filter_manager.foo_filters')
            ->execute(new Request())->getFilters()['single_choice'];

        $expectedChoices = [
            'red',
            'blue',
        ];

        $actualChoices = [];

        foreach ($result->getChoices() as $choice) {
            $actualChoices[] = $choice->getLabel();
        }

        $this->assertEquals($expectedChoices, $actualChoices);
    }

    /**
     * Data provider for testChoicesSort().
     *
     * @return array
     */
    public function getChoicesSortData()
    {
        $out = [];

        // Case #0, sorted in ascending order by term, nothing is prioritized.
        $sortParams = ['type' => '_term', 'order' => 'asc', 'priorities' => []];
        $out[] = ['sortParams' => $sortParams, ['blue', 'green', 'red', 'yellow']];

        // Case #1, sorted in descending order by term, blue is prioritized.
        $sortParams = ['type' => '_term', 'order' => 'desc', 'priorities' => ['blue']];
        $out[] = ['sortParams' => $sortParams, ['blue', 'yellow', 'red', 'green']];

        // Case #2, all items prioritized, so sorting shouldn't matter.
        $sortParams = ['type' => '_term', 'order' => 'desc', 'priorities' => ['blue', 'green', 'red']];
        $out[] = ['sortParams' => $sortParams, ['blue', 'green', 'red', 'yellow']];

        // Case #3, sort items by count, red prioritized.
        $sortParams = ['type' => '_count', 'order' => 'desc', 'priorities' => ['red']];
        $out[] = ['sortParams' => $sortParams, ['red', 'blue', 'green', 'yellow']];

        // Case #3, sort items by count.
        $sortParams = ['type' => '_count', 'order' => 'asc', 'priorities' => []];
        $out[] = ['sortParams' => $sortParams, ['green', 'yellow', 'red', 'blue']];

        return $out;
    }

    /**
     * Check if choices are sorted as expected.
     *
     * @param array $sortParams
     * @param array $expectedChoices
     *
     * @dataProvider getChoicesSortData()
     */
    public function testChoicesSort($sortParams, $expectedChoices)
    {
        $container = new FiltersContainer();

        $filter = new SingleTermChoice();
        $filter->setRequestField('choice');
        $filter->setField('color');
        $filter->setSortType($sortParams);

        $container->set('choice', $filter);

        $manager = new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));

        /** @var ChoicesAwareViewData $result */
        $result = $manager->execute(new Request())->getFilters()['choice'];

        $actualChoices = [];

        foreach ($result->getChoices() as $choice) {
            $actualChoices[] = $choice->getLabel();
        }

        $this->assertEquals($expectedChoices, $actualChoices);
    }

    /**
     * Check if choices min match is working.
     */
    public function testChoicesMinMatch()
    {
        $container = new FiltersContainer();

        $filter = new SingleTermChoice();
        $filter->setRequestField('choice');
        $filter->setField('color');
        $filter->setMinDocCount(4);

        $container->set('choice', $filter);

        $manager = new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));

        /** @var ChoicesAwareViewData $result */
        $result = $manager->execute(new Request())->getFilters()['choice'];
        
        $this->assertEmpty($result->getChoices());
    }
}
