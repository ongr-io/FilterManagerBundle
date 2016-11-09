<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Controller;

use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ManagerController.
 */
class ManagerController extends Controller
{
    /**
     * Renders view with filter manager response.
     *
     * @param Request $request     Request.
     * @param string  $managerName Filter manager name.
     * @param string  $template    Template name.
     *
     * @return Response
     */
    public function managerAction(Request $request, $managerName, $template)
    {
        return $this->render(
            $template,
            $this->getFilterManagerResponse($request, $managerName)
        );
    }

    /**
     * Returns search response results from filter manager.
     *
     * @param Request $request Request.
     * @param string  $managerName    Filter manager name.
     *
     * @return array
     */
    protected function getFilterManagerResponse($request, $managerName)
    {
        return [
            'filter_manager' => $this->get(ONGRFilterManagerExtension::getFilterManagerId($managerName))
                ->handleRequest($request)
        ];
    }

    /**
     * Returns JSON response with search response data.
     *
     * @param Request $request Request.
     * @param string  $name    Filter manager name.
     *
     * @return JsonResponse
     */
    public function jsonAction(Request $request, $managerName)
    {
        $data = $this->get(ONGRFilterManagerExtension::getFilterManagerId($managerName))
            ->handleRequest($request)
            ->getSerializableData();

        $response = new JsonResponse($data);

        if ($request->query->has('pretty')) {
            $response->setEncodingOptions(JSON_PRETTY_PRINT);
        }

        return $response;
    }
}
