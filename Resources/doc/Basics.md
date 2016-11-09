# Basic Usage

## Mandatory configuration fields

Since version `v2.0.0` all filters was unified and from now on all of them shares the same required configuration fields.

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `type`*                | The filter type. (e.g. choice or pager) |
| `request_field`*       | Field name in the request for filter state activation. (e.g. `www.page.com/?page=4` `page` is request field name) |
| `document_field`*      | Specifies the field in the document object to apply filter on. (e.g. `item_color`)       |
| `tags`                 | Array of the filter specific tags that will be accessible at Twig view data.             |
| `relations`            | Read more about `relations` at [dedicated topic here](http://docs.ongr.io/FilterManagerBundle/Relations)           |
| `options`              | Array of the filter specific options. Every filter might have different options, check in the certain filter docs.        |

> `*` are required for every filter.

## Choices sorting

There is an option to sort choices. The sorting can be defined in `options`, here's the list of settings:

| Setting name           | Meaning                                                                                 |
|------------------------|-----------------------------------------------------------------------------------------|
| `sort_type`            | You can sort either by the `_term` which was aggregated or by the `_count` of the terms.|
| `sort_order`           | Specifies the ordering direction. Either ascending or descending.                       |
| `sort_priority`        | Highest priority term names, the first terms to be shown in choices list.               |

## Configuration example
Let's say we have a simple configuration (take a look at the comments after each field).

```yaml
# app/config/config.yml
ongr_filter_manager:
    managers:
        search_list: # <- Filter manager name.
            filters:
                - country # <- Filter name to include in the manager. 
                - pagination
            repository: es.manager.default.product # <- Product document repository service to execute queries on.
    filters:
        country: # <- Filter name
            type: choice # <- Filter type.
            request_field: country # <- Field name in request query. 
            document_field: country # <- Field name in `Product` document.
        pagination:
            type: pager
            request_field: page
            document_field: ~ # Some filters doent require document field so leave it as ~ (null).
            options:
                count_per_page: 12
                max_pages: 8
                choices:
                    - { label: price_desc, field: price, default: true, order: desc }
                    - { label: price_asc, field: price, order: asc }
```

### Usage in the controller

```php
<?php

use ONGR\FilterManagerBundle\DependencyInjection\ONGRFilterManagerExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
    * @param Request $request Request.
    *
    * @return Response
    */
    public function listAction(Request $request)
    {
        // Use `ONGRFilterManagerExtension::getFilterManagerId('search_list')`
        //   function to get filter manager service name.
        $searchList = $this->get(ONGRFilterManagerExtension::getFilterManagerId('search_list'))
                        ->handleRequest($request);
        return $this->render(
          'AppBundle:Product:list.html.twig',
          [
            'filters' => $searchList->getFilters(),  // Returns filters container array
            'products' => $searchList->getResult(),  //Returns result iterator
          ]
        );                  
    }
}
```

From the controller example, there will be 2 variables pass to the template. 
`filters` will contain an array with each filter from active filter manager. In this particular case `filters` array will contain:

- key `color` with an object implementing `ChoicesAwareViewData`
- key `pagination` with an object implementing `PagerAwareViewData`

`products` will contain `DocumentIterator` with the result set.

### Usage in the templates

Firstly let's create the sidebar list of colors with the number of products that have a certain color will be rendered:

```twig
<ul>
    {% for choice in filters.color.choices %}
        <li>
            <a href="{{ path(app.request.attributes.get('_route'), choice.urlParameters) }}">
                {{ choice.label }} ({{ choice.count }})
            </a>
        </li>
    {% endfor %}
</ul>
```

> `choice.urlParameters` contains all attributes with selected filters before, so you don't need to care how to combine the URL.
 
 Next, the product list page. Simply iterate trough product document objects:
 
```twig
 {% for product in products %}
 <p>
     <h3>{{ product.title }}</h3> <small>{{ product.color }}</small>
     <p> {{ product.description }} </p>
     <hr/>
     {{ product.countryCode }} - {{ product.country }} - {{ product.city }} - {{ product.price }} €
 </p>
 {% endfor %}
 ```
 
 And the lastly for this example is pagination. We will grab it from `filters` array and 
  use pre-made twig extension for pagination: 
 
 ```twig
 {{ ongr_paginate(filters.pagination, app.request.attributes.get('_route'), filters.pagination.urlParameters) }}
 ```
 
 > By default it will use filter  manager bundle template which is located here: 
 `vendors/ongr/filter-manager-bundle/Resources/views/Pager/paginate.html.twig`, if it does'nt match your needs, 
  you can provide your own template by third argument in extension function like this:
   
   
 ```twig
 {{ ongr_paginate(filters.pagination, app.request.attributes.get('_route'), filters.pagination.urlParameters, 'AppBundle:YourController:paginate.html.twig') }}
 ```