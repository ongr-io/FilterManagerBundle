<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Twig;

use ONGR\FilterManagerBundle\Pager\Adapters\CountAdapter;
use ONGR\FilterManagerBundle\Pager\PagerService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional pager extension test.
 */
class PagerExtensionTest extends WebTestCase
{
    /**
     * Check if paginate works as expected.
     */
    public function testPaginate()
    {
        $container = self::createClient()->getContainer();
        $counter = new CountAdapter(10);
        $pagerService = new PagerService(
            $counter,
            [
                'limit' => 2,
                'page' => 2,
            ]
        );
        /** @var \Twig_Environment $environment */
        $environment = $container->get('twig');
        $environment->setLoader(new \Twig_Loader_String());
        $paginateTemplate = '{{ paginate_path(route, pager.getFirstPage, parameters) }}
        {{ paginate_path(route, pager.getLastPage, parameters) }}';
        $mainTemplate = "{{ paginate(pager, 'test_page', [], '" . $paginateTemplate . "') }}";
        $result = trim($environment->render($mainTemplate, ['pager' => $pagerService]));
        $this->assertStringStartsWith('/', $result);
        $this->assertStringEndsWith('/?page=5', $result);
    }
}
