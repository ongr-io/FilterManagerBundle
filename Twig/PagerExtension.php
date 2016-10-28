<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Twig;

use ONGR\FilterManagerBundle\Filter\ViewData\PagerAwareViewData;
use Symfony\Component\Routing\RouterInterface;

/**
 * PagerExtension extends Twig with pagination capabilities.
 */
class PagerExtension extends \Twig_Extension
{
    /**
     * Twig extension name.
     */
    const NAME = 'ongr_pager';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'ongr_paginate',
                [$this, 'paginate'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new \Twig_SimpleFunction('ongr_paginate_path', [$this, 'path'], ['is_safe' => []]),
        ];
    }

    /**
     * Renders pagination element.
     *
     * @param \Twig_Environment  $env
     * @param PagerAwareViewData $pager
     * @param string             $route
     * @param array              $parameters
     * @param string             $template
     *
     * @return string
     */
    public function paginate(
        \Twig_Environment $env,
        $pager,
        $route,
        array $parameters = [],
        $template = 'ONGRFilterManagerBundle:Pager:paginate.html.twig'
    ) {

        return $env->render(
            $template,
            ['pager' => $pager, 'route' => $route, 'parameters' => $parameters]
        );
    }

    /**
     * Generates url to certain page.
     *
     * @param string $route
     * @param string $page
     * @param array  $parameters
     *
     * @return string
     */
    public function path($route, $page, array $parameters = [])
    {
        $fieldName = 'page';

        if (isset($parameters['_page'])) {
            $fieldName = $parameters['_page'];
            unset($parameters['_page']);
        }

        $parameters[$fieldName] = $page;

        return $this->router->generate($route, $parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }
}
