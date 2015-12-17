# Using Filter Manager as JSON API

There can be cases when user needs to pass filtered data and filter details not directly to TWIG template but use it by external system. This bundle is able to return results in `json` format.

This example will be based on previous [search page example](search_example.md), so make sure that you have everything set up from that page.

## Requirements

To get filtered results in JSON, user have to define how documents should be converted to JSON format. For this purpose we provide [`SerializableInterface`](https://github.com/ongr-io/FilterManagerBundle/blob/master/SerializableInterface.php) and every document which is defined in manager have to implement it.
This interface have single public method `getSerializableData()` which have to be implemented and should return array representation of single document (this array will be passed to `json_encode()` eventually).
Implementation of this method for `Product` could look like this:

```php
// src/AppBundle/Document/Product.php

public function getSerializableData()
{
    return [
        'title' => $this->title,
        'color' => $this->color,
        'country' => $this->country,
        'weight' => $this->weight,
        'image' => $this->image,
    ];
}
```

Now route to return JSON response has to be registered. Bundle provides default action which can be used:

```yaml
# app/config/routing.yml

ongr_search_page:
    pattern: /search.json
    methods:  [GET]
    defaults:
        _controller: ONGRFilterManagerBundle:Manager:json
        managerName: "search_list"
```

## Usage 

Now if you make a request to `example.com/search.json?pretty&country[0]=Ireland` you will get response similar to this one (some parts are omitted for simplicity):

> Add request parameter `pretty` to get human readable JSON response. It will return one-liner JSON response otherwise.

```json
{
  "count": 1,
  "documents": [
    {
      "title": "aperiam-labore-minus-laudantium",
      "color": "Beige",
      "country": "Ireland",
      "weight": 105,
      "image": "http:\/\/lorempixel.com\/640\/480\/?31837"
    }
  ],
  "filters": {
    "search": {
      "name": "search",
      "state": {
        "active": false,
        "value": null
      },
      "tags": [
        
      ],
      "url_params": {
        "country": [
          "Ireland"
        ]
      },
      "reset_url_params": {
        "country": [
          "Ireland"
        ]
      }
    },
    "color": { ... },
    "country": { ... },
    "weight": { ... },
    "search_pager": { ... },
    "search_sort": { ... }
  },
  "url_parameters": {
    "country": [
      "Ireland"
    ]
  }
}
```