<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\ViewData;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\Search\MatchSearch;
use ONGR\FilterManagerBundle\Relations\ExcludeRelation;
use ONGR\FilterManagerBundle\Search\FiltersContainer;
use ONGR\FilterManagerBundle\Search\FiltersManager;
use Symfony\Component\HttpFoundation\Request;

class FilterStateTest extends AbstractElasticsearchTestCase
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
                        'title' => 'Foo product',
                        'description' => 'Very popular product',
                        'color' => 'red',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Foo cool product',
                        'description' => 'Very popular product',
                        'color' => 'red',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Another cool product',
                        'description' => 'Very popular product',
                        'color' => 'red',
                    ],
                ],
            ],
        ];
    }

    /**
     * Return any kind of filters manager to test.
     *
     * @return FiltersManager
     */
    protected function getFilterManager()
    {
        $container = new FiltersContainer();

        $filter = new MatchSearch();
        $filter->setField('title');
        $filter->setRequestField('q');
        $filter->setResetRelation(new ExcludeRelation(['description_match']));
        $filter->setSearchRelation(new ExcludeRelation(['description_match']));
        $container->set('title_match', $filter);

        $filter = new MatchSearch();
        $filter->setField('description');
        $filter->setRequestField('d');
        $container->set('description_match', $filter);

        return new FiltersManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * @return array
     */
    public function getTestFilterStateData()
    {
        $out = [];

        // Case #0 title filter, both filters are set.
        $out[] = [
            new Request(['q' => 'cool', 'd' => 'product']),
            'title_match',
            'cool',
            ['q' => 'cool'],
            [],
        ];

        // Case #1 description filter, both filters are set.
        $out[] = [
            new Request(['q' => 'cool', 'd' => 'product']),
            'description_match',
            'product',
            ['d' => 'product'],
            ['q' => 'cool'],
        ];

        return $out;
    }

    /**
     * Test if we provide correct filter data.
     *
     * @param Request $request
     * @param string  $filter
     * @param string  $value
     * @param array   $urlParameters
     * @param array   $resetUrlParameters
     *
     * @dataProvider getTestFilterStateData()
     */
    public function testFilterState(Request $request, $filter, $value, $urlParameters, $resetUrlParameters)
    {
        $response = $this->getFilterManager()->execute($request);

        /** @var ViewData $data */
        $data = $response->getFilters()[$filter];

        $this->assertEquals($filter, $data->getState()->getName());
        $this->assertEquals($value, $data->getState()->getValue());
        $this->assertEquals($urlParameters, $data->getState()->getUrlParameters());
        $this->assertEquals($resetUrlParameters, $data->getResetUrlParameters());
    }
}
