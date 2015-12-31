# Search page example

This example will be implemented on empty Symfony Standard project (can be previewed at [this](https://github.com/symfony/symfony-standard/tree/master) link).
Documents will be defined in already created `AppBundle`.

Make sure that you have ESB configured and working before continuing. More info about that in [official documentation](https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/setup.md).

## Sample data
In this example we will use `Product` documents:

```php
# src/AppBundle/Document/Product.php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Document\DocumentTrait;

/**
 * @ES\Document
 */
class Product
{
    use DocumentTrait;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $title;

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
    public $country;

    /**
     * @var string
     *
     * @ES\Property(type="float")
     */
    public $weight;

    /**
     * @var string
     *
     * @ES\Property(type="string", options={"index"="no"})
     */
    public $image;

    /**
     * @var bool
     *
     * @ES\Property(type="boolean")
     */
    public $active;
}

```

## Define filters
Now filters have to be defined in configuration. This example defines single `search_list` manager and some filters:

```yaml
# app/config/config.yml

ongr_filter_manager:
    managers:
        search_list:
            filters:
                - search
                - color
                - country
                - weight
                - search_pager
                - search_sort
            repository: 'es.manager.default.product'
    filters:
        choice:
            color:
                request_field: 'color'
                field: color
        multi_choice:
            country:
                request_field: 'country'
                field: country
        match:
            search:
                request_field: 'q'
                field: title
        range:
            weight:
                request_field: 'weight'
                field: weight
        pager:
            search_pager:
                request_field: 'page'
                count_per_page: 5
        field_value:
            only_active:
                field: 'active'
                value: true
        sort:
            search_sort:
                request_field: 'sort'
                choices:
                  - { label: No sorting, key: score, field: _score }
                  - { label: Heaviest to lightest, key: weight_desc, field: weight, order: desc }
                  - { label: Lightest to heaviest, key: weight_asc, field: weight, order: asc  }
```

## Define route

Next step is to define route for search page, let's add following lines to routing:
```yaml
# app/config/routing.yml

ongr_search_page:
    pattern: /search
    methods:  [GET]
    defaults:
        _controller: ONGRFilterManagerBundle:Manager:manager
        managerName: "search_list"
        template: "AppBundle::search.html.twig"
```

As seen from this example already predefined action `ONGRFilterManagerBundle:Manager:manager` will be used. We provide previously defined `search_list` manager. Search page will be reachable via `/search`.
Last parameter is template to use, see below for more information

## Templating

Our template will be placed in AppBundle's `Resources/views/search.html.twig` file. This template will get `filter_manager` variable which contains all information related to our filtered list.

### Listing documents

Documents can be accessed through `filter_manager.getResult()`. To make a dummy list of results put following code to your template:

```twig
{% for product in filter_manager.getResult() %}
    <ul>
        <li>Title: {{ product.title }}</li>
        <li>Color: {{ product.color }}</li>
        <li>Country: {{ product.country }}</li>
        <li>Weight: {{ product.weight }}</li>
        <li>Image URL: {{ product.image }}</li>
        <li>Active: {{ product.active }}</li>
    </ul>
{% endfor %}
```

### Listing filters

Previously we assigned several filters to `search_list` filter manager. They are accessible via `filter_manager.getFilters()`.

Because filters have different types usually representation of them is different. Example of every filter can be found in dedicated filter type pages:
- [Match](../filter/match.md#usage-in-template-example)
- [Choice](../filter/choice.md#usage-in-template-example)
- [Multi choice](../filter/multi_choice.md#usage-in-template-example)
- [Pager](../filter/pager.md#usage-in-template-example)
- [Sort](../filter/sort.md#usage-in-template-example)
- [Document field](../filter/document_field.md)
