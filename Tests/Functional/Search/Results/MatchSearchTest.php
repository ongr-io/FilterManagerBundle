<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Search\Results;

use ONGR\FilterManagerBundle\Filter\Widget\Search\MatchSearch;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use ONGR\FilterManagerBundle\Test\AbstractFilterManagerResultsTest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class checks if we.
 */
class MatchSearchTest extends AbstractFilterManagerResultsTest
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
                        'color' => 'red',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Foo cool product',
                        'color' => 'red',
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Another cool product',
                        'color' => 'red',
                    ],
                ],
            ],
        ];
    }

    /**
     * Return any kind of filters manager to test.
     *
     * @return FilterManager
     */
    protected function getFilterManager()
    {
        $filter = new MatchSearch();
        $filter->setField('title');
        $filter->setRequestField('q');

        $container = new FilterContainer();
        $container->set('title_match', $filter);

        return new FilterManager(
            $container,
            $this->getManager()->getRepository('TestBundle:Product'),
            new EventDispatcher()
        );
    }

    /**
     * Return your test cases here.
     *
     * @return array
     */
    public function getTestResultsData()
    {
        $out = [];

        // Case #0 empty request.
        $out[] = [new Request(), [1, 2, 3], false];

        // Case #1 foo request.
        $out[] = [new Request(['q' => 'foo']), [1, 2], false];

        // Case #2 cool request.
        $out[] = [new Request(['q' => 'cool']), [2, 3], false];

        return $out;
    }
}
