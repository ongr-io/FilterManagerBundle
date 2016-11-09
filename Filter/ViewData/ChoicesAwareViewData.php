<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\ViewData;

/**
 * This class represents view data with choices.
 */
class ChoicesAwareViewData extends ViewData
{
    /**
     * @var ChoiceAwareViewData[]
     */
    private $choices = [];

    /**
     * @return ViewData\ChoiceAwareViewData[]
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param ViewData\ChoiceAwareViewData[] $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @param ChoiceAwareViewData $choice
     */
    public function addChoice(ChoiceAwareViewData $choice)
    {
        $this->choices[] = $choice;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        $data = parent::getSerializableData();

        foreach ($this->getChoices() as $choice) {
            $data['choices'][] = $choice->getSerializableData();
        }

        return $data;
    }
}
