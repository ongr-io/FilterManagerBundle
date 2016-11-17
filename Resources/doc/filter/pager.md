# Pager Filter  

Before reading about this filter take a look at the [base configuration](http://docs.ongr.io/FilterManagerBundle/Basics) which applies for all filters.

Filter which provides pagination functionality by returning documents for the selected page.

### Pager Filter specific options

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `max_pages`            | Maximum number of pages displayed in pager at once (default `8`).                    |
| `count_per_page`       | Maximum number documents displayed in a single page (default `12`).                  |
  
### Configuration example example:
  
```yaml
# app/config/config.yml
  
ongr_filter_manager:
    # ...
    filters:
        search_pager:
            type: pager
            document_field: ~  # this filter doen not query any specific field in the index
            request_field: page
            options:
                count_per_page: 6
                max_pages: 10
```

### Query composition

This filter does not execute any queries on its own. Instead it sets the `from` and
`size` parameters on the search that is formed by other filters in the same `FilterManager`
instance. These parameters are formed dynamically from the page number specified in the
request.

In case we have the configuration as described above, if we go to the URL `http://127.0.0.1?page=3`,
the state of the filter becomes active and it sets the `from` parameter to 12 and `size` 
parameter to 6.

> Take a look in the [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics) how to pass `Request` object to the controller for execute filtering.

### Usage in template example

`pager` returns `PagerAwareViewData` as the result set for view data. Check the available functions in this class.
But the real goal with any pager is to get paginator bar under the list. With `pager` filter that's easy! 
Simply use the `ongr_paginate` twig function and pass in the `pager` view data object as an argument 
along with the route and the url parameters:

```twig
{{ ongr_paginate(filters.search_pager, app.request.attributes.get('_route'), filters.pager.getUrlParameters()) }}
```

This will render the standard ONGR paginator (`ONGRFilterManagerBundle:Pager:paginate.html.twig`),
if, however, you want to use your own template for the paginator, just provide it to the `ongr_paginate`
function as the fourth parameter.
