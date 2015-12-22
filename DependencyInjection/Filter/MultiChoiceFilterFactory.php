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

/**
 * Factory for multi choice filter.
 */
class MultiChoiceFilterFactory extends ChoiceFilterFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getNamespace()
    {
        return 'ONGR\FilterManagerBundle\Filter\Widget\Choice\MultiTermChoice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'multi_choice';
    }
}
