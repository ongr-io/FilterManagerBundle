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

use ONGR\FilterManagerBundle\Pager\PagerService;
use Symfony\Component\Routing\RouterInterface;

/**
 * PagerExtension extends Twig with pagination capabilities.
 */
class PagerExtension extends \Twig_Extension
{
    /**
     * Twig extension name.
     */
    const NAME = 'ongr.pager';

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $environment;

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
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('paginate_path', [$this, 'path'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renders pagination element.
     *
     * @param PagerService $pager
     * @param string       $route
     * @param array        $parameters
     * @param string       $template
     *
     * @return string
     */
    public function paginate(
        PagerService $pager,
        $route,
        array $parameters = [],
        $template = 'ONGRFilterManagerBundle:Pager:paginate.html.twig'
    ) {
        return $this->environment->render(
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

        // Do not include default values into parameters.
        if ($page <= 1) {
            return $this->router->generate($route, $parameters);
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
