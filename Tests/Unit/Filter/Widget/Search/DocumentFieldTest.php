<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\Widget\Search;

use ONGR\FilterManagerBundle\Filter\Widget\Search\DocumentField;

class DocumentFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for getField() in case field value was not set.
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Field must be set
     */
    public function testGetFieldException()
    {
        $filter = new DocumentField();
        $filter->getField();
    }
}
