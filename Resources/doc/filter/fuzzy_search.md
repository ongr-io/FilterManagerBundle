# Fuzzy search filter

This filter searches for a similar value in the specified field (multiple fields can be separated with comma). Usual use case is search functionality.
Filter uses [Fuzzy query](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html).

## Configuration

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to specify filter value. (e.g. `www.page.com/?request_field=4`)   |
| `field`                | Specifies the field in repository to apply this filter on. (e.g. `item_color`)       |
| `fuzziness`            | Maximum edit distance                                                                |
| `prefix_length`        | The number of initial characters which will not be “fuzzified”                       |
| `max_expansions`       | The maximum number of terms that the fuzzy query will expand to                      |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |
  
Example:
  
```yaml
# app/config/config.yml
    
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - search
            repository: 'es.manager.default.product'
    filters:
        fuzzy:
            search:
                request_field: 'q'
                field: title
```

## Twig view data

View data returned by this filter to be used in template:
 
| Method                  | Value                                            | 
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getTags()               | Lists all tags specified at filter configuration |
| hasTag($tag)            | Checks if filter has the specific tag            |


In twig template, the filter is used similarly to `match` filter.