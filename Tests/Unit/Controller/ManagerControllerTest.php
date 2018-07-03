<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Controller;

use ONGR\FilterManagerBundle\Controller\ManagerController;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests response action.
     */
    public function testGetResponseAction()
    {
        $container = new ContainerBuilder();

        $searchResponse = $this
            ->getMockBuilder('ONGR\FilterManagerBundle\Search\SearchResponse')
            ->disableOriginalConstructor()
            ->getMock();

        $managerMock = $this
            ->getMockBuilder('ONGR\FilterManagerBundle\Search\FilterManager')
            ->disableOriginalConstructor()
            ->setMethods(['handleRequest'])
            ->getMock();

        $managerMock
            ->expects($this->once())
            ->method('handleRequest')
            ->with(new Request())
            ->will($this->returnValue($searchResponse));

        $templating = $this->createMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $templating
            ->expects($this->once())
            ->method('render')
            ->with(
                'template:name.html.twig',
                $this->arrayHasKey('filter_manager')
            )
            ->will($this->returnValue(new Response()));

        $container->set(ONGRFilterManagerExtension::getFilterManagerId('default'), $managerMock);
        $container->set('templating', $templating);

        $controller = new ManagerController();
        $controller->setContainer($container);

        $response = $controller->managerAction(new Request(), 'default', 'template:name.html.twig');

        $this->assertTrue($response->isOk());
    }
}
