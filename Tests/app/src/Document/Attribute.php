<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\NestedType()
 */
class Attribute
{
    /**
     * @var string
     * @ES\Property(type="keyword")
     */
    public $name;

    /**
     * @var string
     * @ES\Property(type="keyword")
     */
    public $value;
}
