Pager Filter  
============  
Filter which provides pagination functionality by returning documents for the selected page.

## Configuration  

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to pass the page number (e.g. `www.page.com/?request_field=4`)    |
| `count_per_page`       | Number of items per page. (default `10`).                                            |
| `max_pages`            | Maximum number of pages displayed in pager at once (default `8`).                    |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |
  
Example:
  
```yaml
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - search_pager
            repository: 'es.manager.default.product'
    filters:
        pager:
            search_pager:
                request_field: 'page'
                count_per_page: 5
```

## Twig view data

View data returned by this filter to be used in template:

| Method                  | Value                                            |
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getPagerService()       | Returns pager service to be used in template     |
| getTags()               | Lists all tags specified at filter configuration |
| hasTag($tag)            | Checks if filter has the specific tag            |

## Usage in template example

This example uses filter defined in [Search example](../examples/search_example.md). To display this filter we would add following code to template:

```twig
{% set pagerFilter = filter_manager.getFilters().search_pager %}
{{ ongr_paginate(pagerFilter.pager, app.request.attributes.get('_route'), pagerFilter.getUrlParameters()) }}
```

This example uses helper provided by [Pager extension](https://github.com/ongr-io/FilterManagerBundle/blob/master/Twig/PagerExtension.php) you can take a look in it for more complex usages.