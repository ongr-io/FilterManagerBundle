<?php

namespace ONGR\FilterManagerBundle\Tests\Unit;

use ONGR\FilterManagerBundle\ONGRFilterManagerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;

class ONGRFilterManagerBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testONGRFilterManagerBundle()
    {
        $extension = new ONGRFilterManagerExtension();
        $container = new ContainerBuilder();
        $container->registerExtension($extension);
        $bundle = new ONGRFilterManagerBundle();
        $bundle->build($container);

        /** @var array $loadedPasses Array of class names of loaded passes */
        $loadedPasses = [];
        /** @var PassConfig $passConfig */
        $passConfig = $container->getCompiler()->getPassConfig();
        foreach ($passConfig->getPasses() as $pass) {
            $classPath = explode('\\', get_class($pass));
            $loadedPasses[] = end($classPath);
        }
        $passName = 'FilterPass';
        $this->assertContains(
            $passName,
            $loadedPasses,
            sprintf(
                "Compiler pass '%s' is not added to container.",
                $passName
            )
        );
    }
}
