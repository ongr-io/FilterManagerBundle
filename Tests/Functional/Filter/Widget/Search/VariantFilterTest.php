<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Filter\Widget\Search;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\Filter\Widget\Search\VariantFilter;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\HttpFoundation\Request;

class VariantFilterTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    public function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'title' => 'Foo',
                    ],
                    [
                        '_id' => 2,
                        'title' => 'Baz',
                        'description' => 'tuna fish',
                        'parent_id' => 1,
                    ],
                    [
                        '_id' => 3,
                        'title' => 'Foo bar',
                        'parent_id' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns filter manager with MatchSearch set.
     *
     * @return FilterManager
     */
    public function getFilterManger()
    {
        $container = new FilterContainer();

        $variant = new VariantFilter();
        $variant->setField('parent_id');

        $container->set('variant', $variant);

        return new FilterManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * Tests if filter works.
     */
    public function testFiltering()
    {
        $this->getManager();

        $result = $this->getFilterManger()->handleRequest(new Request());

        $actual = [];
        foreach ($result->getResult() as $doc) {
            $actual[] = $doc->id;
        }

        $this->assertCount(1, $actual);
    }
}
