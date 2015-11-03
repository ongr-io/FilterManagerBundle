<?php

namespace ONGR\FilterManagerBundle\Tests\Unit\Filters\Widget\Sort;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\ViewData;
use ONGR\FilterManagerBundle\Filters\Widget\Sort\Sort;
use ONGR\FilterManagerBundle\Search\SearchRequest;
use Symfony\Component\HttpFoundation\Request;

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
            ->getMockBuilder('ONGR\FilterManagerBundle\Filters\ViewData')
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
