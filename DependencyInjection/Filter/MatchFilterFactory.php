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
 * Factory for match filter.
 *
 * @deprecated Filter factories will be deleted in 2.0
 */
class MatchFilterFactory extends AbstractFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);

        !isset($configuration['field']) ? : $definition->addMethodCall('setField', [$configuration['field']]);

        !isset($configuration['operator']) ? : $definition
            ->addMethodCall('setOperator', [$configuration['operator']]);
        !isset($configuration['fuzziness']) ? : $definition
            ->addMethodCall('setFuzziness', [$configuration['fuzziness']]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Search\MatchSearch';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'match';
    }
}
