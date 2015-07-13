<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\DependencyInjection\Filter;

use Symfony\Component\DependencyInjection\Definition;

/**
 * Filter factory abstraction.
 */
abstract class AbstractFilterFactory
{
    /**
     * @var array
     */
    private $configuration;
    
    /**
     * Returns filter namespace.
     *
     * @return string
     */
    abstract protected function getNamespace();

    /**
     * Returns filter name.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Configures filter definition.
     *
     * @param Definition $definition    Filter definition.
     * @param array      $configuration Configuration by which filter must be configured.
     */
    protected function configure(Definition $definition, array $configuration)
    {
        $definition->addMethodCall(
            'setRequestField',
            [
                $configuration['request_field'],
            ]
        );
        $definition->addMethodCall(
            'setTags',
            [
                $configuration['tags'],
            ]
        );
    }

    /**
     * Sets configuraton for filter.
     *
     * @param array $configuration
     *
     * @return AbstractFilterFactory
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
        
        return $this;
    }

    /**
     * Returns constructed filter definition.
     *
     * @return Definition
     */
    public function getDefinition()
    {
        $definition = new Definition($this->getNamespace());
        $this->configure($definition, $this->configuration);

        return $definition;
    }
}
