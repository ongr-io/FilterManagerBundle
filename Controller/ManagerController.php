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

use ONGR\FilterManagerBundle\Search\SearchResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerController extends Controller
{
    /**
     * Renders view with filter manager response
     *
     * @param Request $request
     * @param string $managerName
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
     * Returns search response results from filter manager
     *
     * @param Request $request
     * @param string $name
     *
     * @return array
     */
    private function getFilterManagerResponse($request, $name)
    {
        return ['filter_manager' => $this->get(sprintf('ongr_filter_manager.%s', $name))->execute($request)];
    }
}
