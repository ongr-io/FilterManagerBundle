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

use ONGR\FilterManagerBundle\Twig\PagerExtension;
use Symfony\Component\Routing\RouterInterface;

class PagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Returns instance of pager extension.
     *
     * @param bool $includeGenerate
     *
     * @return PagerExtension
     */
    protected function getPager($includeGenerate = true)
    {
        /** @var $router RouterInterface|\PHPUnit_Framework_MockObject_MockObject $environment */
        $router = $this->getMock('\Symfony\Component\Routing\RouterInterface');
        if ($includeGenerate) {
            $router
                ->expects($this->any())
                ->method('generate')
                ->will($this->returnCallback([$this, 'generateRouteCallback']));
        }

        return new PagerExtension($router);
    }

    /**
     * Tests if extension contains functions.
     */
    public function testGetFunctions()
    {
        $pager = $this->getPager(false);
        $this->assertNotEmpty($pager->getFunctions(), 'Pager extension should contain functions.');
    }

    /**
     * Data provider for testGetPath().
     *
     * @return array
     */
    public function getTestGetPathData()
    {
        return [
            // Case #0 Standard case.
            [
                'test1?param=1&page=5',
                'test1',
                5,
                ['param' => 1],
            ],
            // Case #1 Default "page" value should be omitted in URL.
            [
                'test2?param=1',
                'test2',
                1,
                ['param' => 1],
            ],
            // Cases with custom parameter name
            // Case #2 Standard case.
            [
                'test1?param=1&my_custom_page=5',
                'test1',
                5,
                ['param' => 1, '_page' => 'my_custom_page'],
            ],
            // Case #3 Default "page" value should be omitted in URL.
            [
                'test2?param=1',
                'test2',
                1,
                ['param' => 1, '_page' => 'my_custom_page'],
            ],
        ];
    }

    /**
     * Check if path returned is correct.
     *
     * @param string $expected
     * @param string $route
     * @param int    $page
     * @param array  $parameters
     *
     * @dataProvider getTestGetPathData()
     */
    public function testGetPath($expected, $route, $page, $parameters)
    {
        $pager = $this->getPager();
        $this->assertEquals($expected, $pager->path($route, $page, $parameters));
    }

    /**
     * Callback function for router mock.
     *
     * @param string $route
     * @param array  $parameters
     *
     * @return string
     */
    public function generateRouteCallback($route, $parameters)
    {
        $result = $route;
        if (is_array($parameters) && count($parameters)) {
            $result .= '?' . http_build_query($parameters);
        }

        return $result;
    }

    /**
     * Data provider for testPaginate.
     *
     * @return array
     */
    public function getTestPaginateData()
    {
        $out = [];
        $pager = $this
            ->getMockBuilder('ONGR\PagerBundle\Pager\PagerService')
            ->disableOriginalConstructor()
            ->getMock();
        // Case #0.
        $route0 = 'demo_bundle.test_page';
        $parameters0 = [];
        $template0 = 'somebundle:dir:template';
        $expected0 = [
            'pager' => $pager,
            'route' => 'demo_bundle.test_page',
            'parameters' => [],
        ];
        $expectedTemplate0 = 'somebundle:dir:template';
        $out[] = [$pager, $route0, $parameters0, $template0, $expected0, $expectedTemplate0];

        return $out;
    }

    /**
     * Test if method paginate is working as expected.
     *
     * @param PagerService $pager
     * @param string       $route
     * @param array        $parameters
     * @param string       $template
     * @param array        $expected
     * @param string       $expectedTemplate
     *
     * @dataProvider getTestPaginateData
     */
    public function testPaginate(
        $pager,
        $route,
        $parameters,
        $template,
        $expected,
        $expectedTemplate = 'AcmeDemoBundle:List:index.html.twig'
    ) {
        /** @var \Twig_Environment|\PHPUnit_Framework_MockObject_MockObject $environment */
        $environment = $this->getMock('\Twig_Environment');
        $environment
            ->expects($this->once())
            ->method('render')
            ->with($expectedTemplate, $expected);
        $twig = $this->getPager(false);
        $twig->initRuntime($environment);
        $twig->paginate($pager, $route, $parameters, $template);
    }
}
