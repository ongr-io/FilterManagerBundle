<?php

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\Widget\Sort;

use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * Unit test for sorting.
 **/
class SortTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks if sort filter handles missing mode without exceptions.
     */
    public function testWithoutMode()
    {
        $filter = new Sort();
        $filter->setChoices(
            [
                'default' => [
                    'label' => 'default sorting',
                    'field' => 'name',
                    'order' => 'asc',
                    'default' => true,
                ],
            ]
        );
        $filter->setRequestField('sort');

        $state = new FilterState();
        $state->setActive(true);
        $state->setValue('default');

        $modifiedSearch = new Search();
        $filter->modifySearch($modifiedSearch, $state, new SearchRequest(['sort' => 'default']));

        $documentIterator = $this
            ->getMockBuilder('ONGR\ElasticsearchBundle\Result\DocumentIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $viewData = $this
            ->getMockBuilder('ONGR\FilterManagerBundle\Filter\ViewData')
            ->setMethods(['getState', 'addChoice'])
            ->disableOriginalConstructor()
            ->getMock();

        $viewData
            ->expects($this->any())
            ->method('getState')
            ->willReturn($state);


        $filter->getViewData($documentIterator, $viewData);
    }
}
