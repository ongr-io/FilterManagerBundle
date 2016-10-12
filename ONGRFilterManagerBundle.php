<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle;

use ONGR\FilterManagerBundle\DependencyInjection\Compiler\FilterPass;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\ChoiceFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\DateRangeFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\DocumentValueFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\DynamicAggregateFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\FieldValueFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\FuzzyFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\MatchFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\MultiChoiceFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\MultiDynamicAggregateFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\PagerFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\RangeFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\Filter\SortFilterFactory;
use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ONGRFilterManagerBundle.
 */
class ONGRFilterManagerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FilterPass());
    }
}
