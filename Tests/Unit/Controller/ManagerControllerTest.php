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
use ONGR\FilterManagerBundle\Search\FilterManager;
use ONGR\FilterManagerBundle\Search\SearchResponse;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ManagerControllerTest
 * @package ONGR\FilterManagerBundle\Tests\Unit\Controller
 */
class ManagerControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests response action.
     */
    public function testGetResponseAction()
    {
        $container = new ContainerBuilder();

        $searchResponseMock = $this
            ->getMockBuilder(SearchResponse::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $filterManagerMock = $this
            ->getMockBuilder(FilterManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['handleRequest'])
            ->getMock()
        ;

        $filterManagerMock
            ->expects($this->once())
            ->method('handleRequest')
            ->with(new Request())
            ->will($this->returnValue($searchResponseMock))
        ;

        $templatingMock = $this->createMock(EngineInterface::class);
        $templatingMock
            ->expects($this->once())
            ->method('render')
            ->with(
                'template:name.html.twig',
                $this->arrayHasKey('filter_manager')
            )
            ->will($this->returnValue(new Response()))
        ;

        $container->set(ONGRFilterManagerExtension::getFilterManagerId('default'), $filterManagerMock);
        $container->set('templating', $templatingMock);

        $controller = new ManagerController();
        $controller->setContainer($container);

        $response = $controller->managerAction(new Request(), 'default', 'template:name.html.twig');

        $this->assertTrue($response->isOk());
    }
}
