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
 * Factory for dynamic filter.
 */
class DynamicFilterFactory extends AbstractFilterFactory
{
    /**
     * @var array
     */
    private $filterNamespaces = [
        'choice' => 'ONGR\FilterManagerBundle\Filter\Widget\Choice\SingleTermChoice',
        'multi_choice' => 'ONGR\FilterManagerBundle\Filter\Widget\Choice\MultiTermChoice',
        'match' => 'ONGR\FilterManagerBundle\Filter\Widget\Search\MatchSearch',
        'fuzzy' => 'ONGR\FilterManagerBundle\Filter\Widget\Search\FuzzySearch',
        'sort' => 'ONGR\FilterManagerBundle\Filter\Widget\Sort\Sort',
        'pager' => 'ONGR\FilterManagerBundle\Filter\Widget\Pager\Pager',
        'range' => 'ONGR\FilterManagerBundle\Filter\Widget\Range\Range',
        'field_value' => 'ONGR\FilterManagerBundle\Filters\Widget\Search\FieldValue',
        'document_value' => 'ONGR\FilterManagerBundle\Filters\Widget\Search\DocumentValue',
    ];

    /**
     * {@inheritdoc}
     */
    public function configure(Definition $definition, array $configuration)
    {
        parent::configure($definition, $configuration);
        $definition->addMethodCall('setParameters', [$configuration['parameters']]);
        $definition->addMethodCall('setFilterNamespaces', [$this->filterNamespaces]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Dynamic\Dynamic';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dynamic';
    }
}
