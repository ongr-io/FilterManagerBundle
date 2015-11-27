<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\ViewData;

use ONGR\FilterManagerBundle\Filters\ViewData;

/**
 * This class represents view data with choices.
 */
class ChoicesAwareViewData extends ViewData
{
    /**
     * @var Choice[]
     */
    private $choices = [];

    /**
     * @return ViewData\Choice[]
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param ViewData\Choice[] $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @param Choice $choice
     */
    public function addChoice(Choice $choice)
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
