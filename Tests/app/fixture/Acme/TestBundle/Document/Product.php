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
use ONGR\ElasticsearchBundle\Document\AbstractDocument;
use ONGR\FilterManagerBundle\SerializableInterface;

/**
 * @ES\Document(type="product")
 */
class Product extends AbstractDocument implements SerializableInterface
{

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
     * @ES\Property(type="string", name="categories", options={"index"="not_analyzed"})
     */
    public $categories;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="manufacturer", options={"index"="not_analyzed"})
     */
    public $manufacturer;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="color", options={"index"="not_analyzed"})
     */
    public $color;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="size", options={"index"="not_analyzed"})
     */
    public $size;

    /**
     * @var int
     *
     * @ES\Property(type="integer", name="items", options={"index"="not_analyzed"})
     */
    public $items;

    /**
     * @var string
     *
     * @ES\Property(type="string", name="words", options={"index"="not_analyzed"})
     */
    public $words;

    /**
     * @var string
     *
     * @ES\Property(type="date", name="date")
     */
    public $date;

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        return [
            '_id' => $this->getId(),
            'title' => $this->title,
            'color' => $this->color,
        ];
    }
}
