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
use ONGR\FilterManagerBundle\DependencyInjection\Filter\MultiDynamicAggregateFilterFactory;
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
        
        /** @var ONGRFilterManagerExtension $extension */
        $extension = $container->getExtension('ongr_filter_manager');
        $extension->addFilterFactory(new ChoiceFilterFactory());
        $extension->addFilterFactory(new MultiChoiceFilterFactory());
        $extension->addFilterFactory(new MatchFilterFactory());
        $extension->addFilterFactory(new FuzzyFilterFactory());
        $extension->addFilterFactory(new SortFilterFactory());
        $extension->addFilterFactory(new PagerFilterFactory());
        $extension->addFilterFactory(new RangeFilterFactory());
        $extension->addFilterFactory(new DateRangeFilterFactory());
        $extension->addFilterFactory(new FieldValueFactory());
        $extension->addFilterFactory(new DocumentValueFactory());
        $extension->addFilterFactory(new DynamicAggregateFactory());
        $extension->addFilterFactory(new MultiDynamicAggregateFilterFactory());
        $extension->addFilterFactory(new DynamicAggregateFactory());

        $container->addCompilerPass(new FilterPass());
    }
}
