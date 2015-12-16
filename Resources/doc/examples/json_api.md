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

Now if you make a request to `example.com/search.json?pretty&country[0]=Ireland` you will get response similar to this one:

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
    "color": {
      "name": "color",
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
      },
      "choices": [
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland"
            ],
            "color": "Beige"
          },
          "label": "Beige",
          "mode": null,
          "count": 1
        }
      ]
    },
    "country": {
      "name": "country",
      "state": {
        "active": true,
        "value": [
          "Ireland"
        ]
      },
      "tags": [
        
      ],
      "url_params": {
        "country": [
          "Ireland"
        ]
      },
      "reset_url_params": [
        
      ],
      "choices": [
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland",
              "Bosnia and Herzegovina"
            ]
          },
          "label": "Bosnia and Herzegovina",
          "mode": null,
          "count": 1
        },
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland",
              "British Indian Ocean Territory (Chagos Archipelago)"
            ]
          },
          "label": "British Indian Ocean Territory (Chagos Archipelago)",
          "mode": null,
          "count": 1
        },
        {
          "active": true,
          "default": false,
          "url_params": [
            
          ],
          "label": "Ireland",
          "mode": null,
          "count": 1
        },
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland",
              "Puerto Rico"
            ]
          },
          "label": "Puerto Rico",
          "mode": null,
          "count": 1
        },
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland",
              "South Georgia and the South Sandwich Islands"
            ]
          },
          "label": "South Georgia and the South Sandwich Islands",
          "mode": null,
          "count": 1
        }
      ]
    },
    "weight": {
      "name": "weight",
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
      },
      "min_bound": 20,
      "max_bound": 105
    },
    "search_pager": {
      "name": "search_pager",
      "state": {
        "active": false,
        "value": 1
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
      },
      "pager": {
        "page": 1,
        "last_page": 1
      }
    },
    "search_sort": {
      "name": "search_sort",
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
      },
      "choices": [
        {
          "active": false,
          "default": true,
          "url_params": {
            "country": [
              "Ireland"
            ],
            "sort": "score"
          },
          "label": "No sorting",
          "mode": null,
          "count": 0
        },
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland"
            ],
            "sort": "weight_desc"
          },
          "label": "Heaviest to lightest",
          "mode": null,
          "count": 0
        },
        {
          "active": false,
          "default": false,
          "url_params": {
            "country": [
              "Ireland"
            ],
            "sort": "weight_asc"
          },
          "label": "Lightest to heaviest",
          "mode": null,
          "count": 0
        }
      ]
    }
  },
  "url_parameters": {
    "country": [
      "Ireland"
    ]
  }
}
```