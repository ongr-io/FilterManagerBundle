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

use Doctrine\Common\Collections\ArrayCollection;
use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\FilterManagerBundle\SerializableInterface;

/**
 * @ES\Index()
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
     * @ES\Property(type="text", settings={"fielddata"="true"})
     */
    public $sku;

    /**
     * @var string
     *
     * @ES\Property(type="text")
     */
    public $title;

    /**
     * @var string
     *
     * @ES\Property(type="text")
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
     * @ES\Property(type="keyword")
     */
    public $category;

    /**
     * @var string
     *
     * @ES\Property(type="keyword")
     */
    public $manufacturer;

    /**
     * @var string
     *
     * @ES\Property(type="keyword")
     */
    public $color;

    /**
     * @var string
     *
     * @ES\Property(type="keyword")
     */
    public $size;

    /**
     * @var int
     *
     * @ES\Property(type="integer")
     */
    public $items;

    /**
     * @var string
     *
     * @ES\Property(type="keyword")
     */
    public $words;

    /**
     * @var string
     *
     * @ES\Property(type="date")
     */
    public $date;

    /**
     * @var Attribute[]
     *
     * @ES\Embedded(class="App\Document\Attribute")
     */
    public $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
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
