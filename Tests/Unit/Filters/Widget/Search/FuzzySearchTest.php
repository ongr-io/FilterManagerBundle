<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Filters\Widget\Search;

use ONGR\FilterManagerBundle\Filters\Widget\Search\FuzzySearch;

class FuzzySearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests modifySearch method, with one and with multiple search fields.
     */
    public function testModifySearch()
    {
        $mockFilterState = $this->getMockBuilder('ONGR\FilterManagerBundle\Filters\FilterState')->getMock();
        $mockFilterState->expects($this->exactly(2))
            ->method('isActive')
            ->will($this->returnValue(true));

        $fuzzySearch = new FuzzySearch();

        $mockSearch = $this->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')->getMock();
        $mockSearch->expects($this->once())
            ->method('addQuery')
            ->with($this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Query\FuzzyQuery'), $this->equalTo('must'));

        $fuzzySearch->setField('name');
        $fuzzySearch->modifySearch($mockSearch, $mockFilterState);

        $mockSearch = $this->getMockBuilder('ONGR\ElasticsearchBundle\DSL\Search')->getMock();
        $mockSearch->expects($this->exactly(1))
            ->method('addQuery')
            ->with($this->isInstanceOf('ONGR\ElasticsearchBundle\DSL\Query\BoolQuery'), $this->equalTo('must'));

        $fuzzySearch->setField('name,age,address');
        $fuzzySearch->modifySearch($mockSearch, $mockFilterState);
    }
}
