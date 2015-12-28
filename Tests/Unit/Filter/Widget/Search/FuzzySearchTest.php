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

use ONGR\FilterManagerBundle\Filter\Widget\Search\FuzzySearch;

class FuzzySearchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests modifySearch method, with one and with multiple search fields.
     */
    public function testModifySearch()
    {
        $mockFilterState = $this->getMockBuilder('ONGR\FilterManagerBundle\Filter\FilterState')->getMock();
        $mockFilterState->expects($this->exactly(2))
            ->method('isActive')
            ->will($this->returnValue(true));

        $fuzzySearch = new FuzzySearch();

        $mockSearch = $this->getMockBuilder('ONGR\ElasticsearchDSL\Search')->getMock();
        $mockSearch->expects($this->once())
            ->method('addQuery')
            ->with($this->isInstanceOf('ONGR\ElasticsearchDSL\Query\FuzzyQuery'), $this->equalTo('must'));

        $fuzzySearch->setField('name');
        $fuzzySearch->modifySearch($mockSearch, $mockFilterState);

        $mockSearch = $this->getMockBuilder('ONGR\ElasticsearchDSL\Search')->getMock();
        $mockSearch->expects($this->exactly(1))
            ->method('addQuery')
            ->with($this->isInstanceOf('ONGR\ElasticsearchDSL\Query\BoolQuery'), $this->equalTo('must'));

        $fuzzySearch->setField('name,age,address');
        $fuzzySearch->modifySearch($mockSearch, $mockFilterState);
    }
}
