<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Filters\Widget;

use ONGR\FilterManagerBundle\Filters\FilterInterface;
use ONGR\FilterManagerBundle\Filters\FilterState;
use ONGR\FilterManagerBundle\Filters\Relations\RelationsAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class generalises filters using single field value from request.
 */
abstract class AbstractSingleRequestValueFilter implements FilterInterface
{
    use RelationsAwareTrait;

    /**
     * @var string
     */
    private $requestField;

    /**
     * @var array
     */
    private $tags = [];

    /**
     * {@inheritdoc}
     */
    public function getState(Request $request)
    {
        $state = new FilterState();
        $value = $request->get($this->getRequestField());

        if (isset($value) && $value !== '') {
            $state->setActive(true);
            $state->setValue($value);
            $state->setUrlParameters([$this->getRequestField() => $value]);
        }

        return $state;
    }

    /**
     * @return string
     */
    public function getRequestField()
    {
        return $this->requestField;
    }

    /**
     * @param string $requestField
     */
    public function setRequestField($requestField)
    {
        $this->requestField = $requestField;
    }

    /**
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param string $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }
}
