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
 * Factory for field_value factory.
 */
class DocumentValueFactory extends AbstractFilterFactory
{
    /**
     * Configures filter definition.
     *
     * @param Definition $definition    Filter definition.
     * @param array      $configuration Configuration by which filter must be configured.
     */
    protected function configure(Definition $definition, array $configuration)
    {
        $definition->addMethodCall(
            'setTags',
            [
                $configuration['tags'],
            ]
        );
        $definition->addMethodCall(
            'setDocumentField',
            [
                $configuration['document_field'],
            ]
        );
        $definition->addMethodCall(
            'setField',
            [
                $configuration['field'],
            ]
        );
    }
    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Search\DocumentValue';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'document_value';
    }
}
