<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Test;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use ONGR\FilterManagerBundle\Search\FilterManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class is able to test results from filters manager.
 */
abstract class AbstractFilterManagerResultsTest extends AbstractElasticsearchTestCase
{
    /**
     * Return any kind of filter manager to test.
     *
     * @return FilterManager
     */
    abstract protected function getFilterManager();

    /**
     * Return your test cases here.
     *
     * @return array
     */
    abstract public function getTestResultsData();

    /**
     * This method asserts if search request gives expected results.
     *
     * @param Request $request     Http request.
     * @param array   $ids         Array of document ids to assert.
     * @param bool    $assertOrder Set true if order of results lso should be asserted.
     *
     * @dataProvider getTestResultsData()
     */
    public function testResults(Request $request, $ids, $assertOrder = false)
    {
        $actual = array_map(
            [$this, 'fetchDocumentId'],
            iterator_to_array($this->getFilterManager()->handleRequest($request)->getResult())
        );

        if (!$assertOrder) {
            sort($actual);
            sort($ids);
        }

        $this->assertEquals($ids, $actual);
    }

    /**
     * Returns document id.
     *
     * @param object $doc
     *
     * @return string
     */
    protected function fetchDocumentId($doc)
    {
        return $doc->id;
    }
}
