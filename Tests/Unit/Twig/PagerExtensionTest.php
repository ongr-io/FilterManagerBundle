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

use ONGR\FilterManagerBundle\Pager\PagerService;
use ONGR\FilterManagerBundle\Twig\PagerExtension;
use Symfony\Component\Routing\RouterInterface;

class PagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PagerExtension|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pagerExtension;

    /**
     * Before a test method is run, a template method called setUp() is invoked.
     */
    public function setUp()
    {
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->pagerExtension = new PagerExtension($this->router);
    }

    /**
     * Tests getFunctions.
     */
    public function testGetFunctions()
    {
        $result = $this->pagerExtension->getFunctions();
        $this->assertNotEmpty($result);
    }

    /**
     * Tests getName.
     */
    public function testGetName()
    {
        $result = $this->pagerExtension->getName();
        $this->assertNotEmpty($result);
    }

    /**
     * Tests Paginate.
     */
    public function testPaginate()
    {
        /** @var \Twig_Environment|\PHPUnit_Framework_MockObject_MockObject $managerMock */
        $twigEnvironment = $this->getMockBuilder('\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $twigEnvironment->expects($this->once())
            ->method('render')
            ->will($this->returnValue('result'));

        /** @var PagerService|\PHPUnit_Framework_MockObject_MockObject $managerMock */
        $pager = $this->getMockBuilder('ONGR\FilterManagerBundle\Pager\PagerService')
            ->disableOriginalConstructor()
            ->getMock();

        $route = 'route_name';
        $parameters = [];
        $template = 'tpl';

        $result = $this->pagerExtension->paginate($twigEnvironment, $pager, $route, $parameters, $template);
        $this->assertNotEmpty($result);
    }

    /**
     * Data provider for testPath test.
     *
     * @return array
     */
    public function testPathDataProvider()
    {
        return [
            [['_page' => 'val'], 1],
            [['_page' => 'val'], 2],
            [[], 1],
            [[], 2],
        ];
    }

    /**
     * Tests path.
     *
     * @param array $parameters
     * @param int   $page
     *
     * @dataProvider testPathDataProvider
     */
    public function testPath(array $parameters, $page)
    {
        $route = 'route_name';

        $this->router->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('/'));

        $result = $this->pagerExtension->path($route, $page, $parameters);
        $this->assertEquals('/', $result);
    }
}
