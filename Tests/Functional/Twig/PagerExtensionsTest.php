<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Twig;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\Twig\PagerExtension;
use Symfony\Component\Routing\RouterInterface;
use ONGR\FilterManagerBundle\Pager\PagerService;
use ONGR\FilterManagerBundle\Pager\Adapters\CountAdapter;

class PagerExtensionsTest extends AbstractElasticsearchTestCase
{
    /**
     * Check if paginate works as expected.
     */
    public function testPaginate()
    {
        $container = self::createClient()->getContainer();
        $counter = new CountAdapter(10);
        $pagerService = new PagerService($counter);
        $pagerService->setLimit(2);
        $pagerService->setPage(2);
        /** @var \Twig_Environment $environment */
        $environment = $container->get('twig');
        $environment->setLoader(new \Twig_Loader_String());
        $paginateTemplate = '{{ ongr_paginate_path(route, pager.getFirstPage, parameters) }}
        {{ ongr_paginate_path(route, pager.getLastPage, parameters) }}';
        $mainTemplate = "{{ ongr_paginate(pager, 'test_page', [], '" . $paginateTemplate . "') }}";
        $result = trim($environment->render($mainTemplate, ['pager' => $pagerService]));
        $this->assertStringStartsWith('/', $result);
        $this->assertStringEndsWith('/?page=5', $result);
    }

    /**
     * Data provider for testPath test.
     *
     * @return array
     */
    public function testPathDataProvider()
    {
        return [
            ['test_page', '/', 1],
            ['test_page', '/?page=2', 2],
            ['test_page', '/?page=3', 3],
        ];
    }

    /**
     * Checks if path method returns correct paths.
     *
     * @param string $route
     * @param string $expectedResult
     * @param int    $page
     *
     * @dataProvider testPathDataProvider
     */
    public function testPathFunction($route, $expectedResult, $page)
    {
        /** @var RouterInterface $router */
        $router = $this->getContainer()->get('router');
        $extension = new PagerExtension($router);

        $parameters = [];
        $result = $extension->path($route, $page, $parameters);

        $this->assertEquals($expectedResult, $result);
    }
}
