<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\app\fixture\Acme\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\DocumentInterface;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * @ES\Document(type="product")
 */
class Product implements DocumentInterface
{
    use DocumentTrait;

    /**
     * @var string
     *
     * @ES\Property(type="boolean", name="active")
     */
    public $active;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="sku")
     */
    public $sku;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="title")
     */
    public $title;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="description")
     */
    public $description;

    /**
     * @var string
     *
     * @ES\Property(type="geo_point", name="location")
     */
    public $location;

    /**
     * @var int
     *
     * @ES\Property(type="integer", name="stock")
     */
    public $stock;

    /**
     * @var int
     *
     * @ES\Property(type="integer", name="deliveryTime")
     */
    public $deliveryTime;

    /**
     * @var float
     *
     * @ES\Property(type="float", name="price")
     */
    public $price;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="categories", index="not_analyzed")
     */
    public $categories;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="manufacturer", index="not_analyzed")
     */
    public $manufacturer;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="color", index="not_analyzed")
     */
    public $color;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="size", index="not_analyzed")
     */
    public $size;
}
