<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Pager;

use App\Document\Product;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\HttpFoundation\Request;

class PagerTest extends AbstractElasticsearchTestCase
{
    /**
     * @return array
     */
    protected function getDataArray()
    {
        return [
            Product::class => [
                [
                    '_id' => 1,
                    'color' => 'red',
                    'manufacturer' => 'a',
                    'stock' => 1,
                ],
                [
                    '_id' => 2,
                    'color' => 'blue',
                    'manufacturer' => 'a',
                    'stock' => 2,
                ],
                [
                    '_id' => 3,
                    'color' => 'red',
                    'manufacturer' => 'b',
                    'stock' => 3,
                ],
                [
                    '_id' => 4,
                    'color' => 'blue',
                    'manufacturer' => 'c',
                    'stock' => 4,
                ],
                [
                    '_id' => 5,
                    'color' => 'blue',
                    'manufacturer' => 'b',
                    'stock' => 5,
                ],
            ],
        ];
    }

    /**
     * Test pager filter.
     */
    public function testPager()
    {
        /** @var FilterManager $manager */
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('pager'));
        $result = $manager->handleRequest(new Request())->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals([5,4,3], $actual);
    }

    /**
     * Test pager filter on second page.
     */
    public function testPagerOnSecondPage()
    {
        /** @var FilterManager $manager */
        $manager = $this->getContainer()->get(ONGRFilterManagerExtension::getFilterManagerId('pager'));
        $result = $manager->handleRequest(new Request(['page' => 2]))->getResult();

        $actual = [];
        foreach ($result as $document) {
            $actual[] = $document->id;
        }

        $this->assertEquals([2,1], $actual);
    }

    protected function setUp()
    {
        $this->getIndex(Product::class);
    }
}
