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
            ->getMockBuilder('ONGR\FilterManagerBundle\Search\FiltersManager')
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();

        $managerMock
            ->expects($this->once())
            ->method('execute')
            ->with(new Request())
            ->will($this->returnValue($searchResponse));

        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with(
                'template:name.html.twig',
                $this->arrayHasKey('filter_manager')
            )
            ->will($this->returnValue(new Response()));

        $container->set('ongr_filter_manager.foo', $managerMock);
        $container->set('templating', $templating);

        $controller = new ManagerController();
        $controller->setContainer($container);

        $response = $controller->managerAction(new Request(), 'foo', 'template:name.html.twig');

        $this->assertTrue($response->isOk());
    }
}
