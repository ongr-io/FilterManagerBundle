<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\FilterManagerBundle\Tests\app\fixture\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Collection\Collection;
use ONGR\FilterManagerBundle\SerializableInterface;

/**
 * @ES\Document(type="product")
 */
class Product implements SerializableInterface
{
    /**
     * @var string
     *
     * @ES\Id()
     */
    public $id;

    /**
     * @var string
     *
     * @ES\Property(type="boolean")
     */
    public $active;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    public $sku;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    public $title;

    /**
     * @var string
     *
     * @ES\Property(type="string")
     */
    public $description;

    /**
     * @var string
     *
     * @ES\Property(type="geo_point")
     */
    public $location;

    /**
     * @var int
     *
     * @ES\Property(type="integer")
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
     * @ES\Property(type="float")
     */
    public $price;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $categories;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $manufacturer;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $color;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $size;

    /**
     * @var int
     *
     * @ES\Property(type="integer", options={"index"="not_analyzed"})
     */
    public $items;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $words;

    /**
     * @var string
     *
     * @ES\Property(type="date", options={"format":"strict_date_optional_time"})
     */
    public $date;

    /**
     * @var Attribute[]
     *
     * @ES\Embedded(class="TestBundle:Attribute", multiple=true)
     */
    public $attributes;

    public function __construct()
    {
        $this->attributes = new Collection();
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        return [
            '_id' => $this->id,
            'title' => $this->title,
            'color' => $this->color,
        ];
    }
}
