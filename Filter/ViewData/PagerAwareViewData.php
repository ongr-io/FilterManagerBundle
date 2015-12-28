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
use ONGR\FilterManagerBundle\Pager\PagerService;

/**
 * This class represents view data with choices.
 */
class PagerAwareViewData extends ViewData
{
    /**
     * @var PagerService
     */
    private $pager;

    /**
     * @param PagerService $pager
     */
    public function setPager($pager)
    {
        $this->pager = $pager;
    }

    /**
     * @return PagerService
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        $data = parent::getSerializableData();

        $data['pager'] = [
            'page' => $this->pager->getPage(),
            'last_page' => $this->pager->getLastPage(),
        ];

        return $data;
    }
}
