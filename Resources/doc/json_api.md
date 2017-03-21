# Using Filter Manager as JSON API

There can be cases when user needs to pass filtered data and filter details not directly to TWIG template but use it by 
external system. This bundle is able to return results in `json` format.

## Requirements

Basically, all you need to achieve this is to call the appropriate controller action
that comes with the bundle:

```yaml
# app/config/routing.yml

ongr_search_page:
    path: /search.json
    methods:  [GET]
    defaults:
        _controller: ONGRFilterManagerBundle:Manager:json
        managerName: "search_list"
```

Internally the bundle uses `JMSSerializerBundle` for the serialization of the documents,
therefore if you want to have more control over how to define specific document serialization, 
please refer to the [bundle documentation](http://jmsyst.com/libs/serializer)

## Usage Example

This example will be based on [search page example](http://docs.ongr.io/FilterManagerBundle/examples/search_example), 
so make sure to check that page to get the idea for the documents and filters that are being used.

Now if you make a request to `example.com/search.json?pretty&country[0]=Ireland` you will get response similar to this one (some parts are omitted and denoted `{ ... }` for simplicity):

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