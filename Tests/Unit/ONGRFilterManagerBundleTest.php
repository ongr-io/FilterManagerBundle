<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit;

use ONGR\FilterManagerBundle\ONGRFilterManagerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ONGRFilterManagerBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testing if compiler pass have been loaded.
     *
     * @return array
     */
    public function getTestPassExistsData()
    {
        return [
            ['ONGR\FilterManagerBundle\DependencyInjection\Compiler\FilterPass'],
        ];
    }

    /**
     * Tests if compiler pass is added to container builder.
     *
     * @param string $pass Compiler pass instance in string.
     *
     * @dataProvider getTestPassExistsData
     */
    public function testPassExists($pass)
    {
        $container = new ContainerBuilder();
        $bundle = new ONGRFilterManagerBundle();
        $bundle->build($container);
        $this->assertCount(
            1,
            array_filter(
                $container->getCompilerPassConfig()->getPasses(),
                function ($val) use ($pass) {
                    if (get_class($val) === $pass) {
                        return true;
                    }

                    return false;
                }
            )
        );
    }
}
