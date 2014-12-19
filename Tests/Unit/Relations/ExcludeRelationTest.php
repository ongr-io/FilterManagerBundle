<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\Unit\Relations;

use ONGR\ElasticsearchBundle\Test\ElasticsearchTestCase;
use ONGR\FilterManagerBundle\Relations\ExcludeRelation;

class ExcludeRelationTest extends ElasticsearchTestCase
{
    /**
     * Data provider for testFuzzyQuery().
     *
     * @return array
     */
    public function getTestExcludeRelationData()
    {
        $out = [];

        $out[] = [['a', 'b', 'c'], 'c', false];
        $out[] = [['a', 'b', 'c'], 'd', true];
        $out[] = [[], 'd', true];

        return $out;
    }

    /**
     * Test Exclude Relation.
     *
     * @param array  $relations
     * @param string $name
     * @param bool   $expected
     *
     * @dataProvider getTestExcludeRelationData
     */
    public function testExcludeRelation($relations, $name, $expected)
    {
        $excludeRelation = new ExcludeRelation($relations);
        $this->assertEquals($expected, $excludeRelation->isRelated($name));
    }
}
