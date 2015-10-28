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

use ONGR\FilterManagerBundle\Relations\LogicalJoin\AndRelation;

class AndRelationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testFuzzyQuery().
     *
     * @return array
     */
    public function getTestAndRelationData()
    {
        $out = [];

        $out[] = [[true, true, null], true];
        $out[] = [[true, false, null], false];
        $out[] = [[], true];
        $out[] = [[null], true];

        return $out;
    }

    /**
     * Test And Relation.
     *
     * @param array $relations
     * @param bool  $expected
     *
     * @dataProvider getTestAndRelationData
     */
    public function testAndRelation($relations, $expected)
    {
        $andRelation = new AndRelation();

        foreach ($relations as $result) {
            if (isset($result)) {
                $mock = $this->getMock('ONGR\FilterManagerBundle\Relations\RelationInterface');
                $mock->expects($this->any())->method('isRelated')->will($this->returnValue($result));
                $andRelation->addRelation($mock);
            } else {
                $andRelation->addRelation();
            }
        }

        $this->assertEquals($expected, $andRelation->isRelated($expected));
    }
}
