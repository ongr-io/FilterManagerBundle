# Searching in specific fields with boosting

In this example simple search page will be implemented. Multiple fields with different boosting will be used in search.
In configuration we will search in `title` and `color` fields. `color` will be boosted by `2` and `title` by `0.5` (deboosting).
Boosting is done by suffixing field name with `^{boost_factor}`, e.g `title^2` ,`color^0.5`. 

## Preparation

First of all Elasticsearch bundle and FilterManager bundle has to be configured:

```yml
# app/config/config.yml

ongr_elasticsearch:
    connections:
        default:
            hosts:
                - 127.0.0.1:9200
            index_name: persons
    managers:
        default:
            connection: default
            mappings:
                - AppBundle
                
ongr_filter_manager:
    managers:
        field_boosting:
            filters:
                - boosted_search
            repository: 'es.manager.default.product'
        match:
            boosted_search:
                request_field: 'q'
                field: title^2,color^0.5                
```

Next step is to define document:

```php
// src/AppBundle/Document/Product.php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Document(type="product")
 */
class Product
{
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
    public $color;
}
```

Define route for listing page:

```yml
# app/config/routing.yml

ongr_boosted_search_page:
    pattern: /search_boosted
    defaults:
        _controller: ONGRFilterManagerBundle:Manager:manager
        managerName: "field_boosting"
        template: "AppBundle::boosted_search.html.twig"
```

Define template:

```twig
# src/AppBundle/Resources/views/boosted_search.html.twig

<form action="{{ path(app.request.attributes.get('_route')) }}" method="get">
    <input name="q" placeholder="Search..." value="{{ filter_manager.getFilters().boosted_search.getState().getValue() }}">
    <input type="submit" value="Search">
</form>

{% for product in filter_manager.getResult() %}
    <ul>
        <li>Title: {{ product.title }}</li>
        <li>Color: {{ product.color }}</li>
    </ul>
{% endfor %}
```

## Importing sample data

Create index with `app/console ongr:es:index:create`.

Create file `products.json` with following content:

```json
[
{"count":4,"date":"2015-12-21T14:42:17+0200"},
{"_type":"product","_id":"1","_source":{"title":"iPhone","color":"White"}},
{"_type":"product","_id":"2","_source":{"title":"White iPhone","color":"White"}},
{"_type":"product","_id":"3","_source":{"title":"Black iPhone","color":"Black"}},
{"_type":"product","_id":"4","_source":{"title":"iPad with white edges","color":"Golden"}}
]
```

Import this data with `app/console ongr:es:index:import products.json` command.

## Usage

Now if you go to `example.com/search_boosted` you should see page with search form and list of products.

If you type in "white" in input and press `Search` then these results (in this order) will be shown:
- `White iPhone` is first because it has word "white" in both fields `title` and `color`.
- `iPhone` is second because it has "white" in `color` field which is boosted more than `title`.
- `iPad with white edges` is last because it has "white" only in `title`.
