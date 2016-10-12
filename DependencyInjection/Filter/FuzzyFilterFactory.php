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
 * Factory for fuzzy filter.
 *
 * @deprecated Filter factories will be deleted in 2.0
 */
class FuzzyFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        $definition->addMethodCall('setField', [$configuration['field']]);
        
        !isset($configuration['fuzziness']) ? : $definition
            ->addMethodCall('setFuzziness', [$configuration['fuzziness']]);
        !isset($configuration['prefix_length']) ? : $definition
            ->addMethodCall('setPrefixLength', [$configuration['prefix_length']]);
        !isset($configuration['max_expansions']) ? : $definition
            ->addMethodCall('setMaxExpansions', [$configuration['max_expansions']]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Search\FuzzySearch';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fuzzy';
    }
}
