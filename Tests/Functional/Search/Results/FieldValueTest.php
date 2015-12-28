<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Functional\Search\Results;

use ONGR\FilterManagerBundle\Filter\Widget\Search\FieldValue;
use ONGR\FilterManagerBundle\Search\FilterContainer;
use ONGR\FilterManagerBundle\Search\FilterManager;
use ONGR\FilterManagerBundle\Test\AbstractFilterManagerResultsTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * Functional test for field_value filter.
 */
class FieldValueTest extends AbstractFilterManagerResultsTest
{
    /**
     * {@inheritDoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'product' => [
                    [
                        '_id' => 1,
                        'active' => true,
                    ],
                    [
                        '_id' => 2,
                        'active' => false,
                    ],
                    [
                        '_id' => 3,
                        'active' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterManager()
    {
        $filter = new FieldValue();
        $filter->setField('active');
        $filter->setValue(true);

        $container = new FilterContainer();
        $container->set('field_value', $filter);

        return new FilterManager($container, $this->getManager()->getRepository('AcmeTestBundle:Product'));
    }

    /**
     * {@inheritDoc}
     */
    public function getTestResultsData()
    {
        return [
            'Only active products should be returned' => [new Request(), [1, 3]],
        ];
    }
}
