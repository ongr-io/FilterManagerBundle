<?php

namespace ONGR\FilterManagerBundle\Tests\Unit\Filter\Widget\Choice;

use ONGR\FilterManagerBundle\Filter\Widget\Choice\SingleTermChoice;

class SingleTermChoiceTest extends \PHPUnit_Framework_TestCase
{
    public function testShowZeroChoicesGetterAndSetter()
    {
        $filter = new SingleTermChoice();
        $filter->setShowZeroChoices(true);
        $this->assertTrue($filter->getShowZeroChoices());
    }
}
