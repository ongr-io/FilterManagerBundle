# Multi Dynamic Aggregate Filter

This filter is very similar to the [Dynamic Aggregate](dynamic_aggregate.md) filter. It also aggregates
data from a specific value of the field within a nested object of a document and groups this data by the 
values provided in a separate field. However, with this filter more than one request value can be provided 
for every group of aggregated values. This is essentially the only difference between the two aggregate 
filters, therefore, the usage of them is identical.

## Configuration 

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to view the selected page. (e.g. `www.page.com/?request_field=4`) |
| `name_field`           | Specifies the field in the repository that the results will be grouped by            |
| `field`                | Specifies the field in repository to apply this filter on.                           |
| `sort`                 | Sorts the choices based on your configuration.                                       |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |

> Important note! Here `name_field` and `field` values should both point to the fields in the nested object of the
document, however the `field` must specify both the `path` and the `field` properties separated by the `>` sign 
(e.g. `field = 'attributes>attributes.value'`).

Configuration Example:
  
```yaml
  
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - attributes
            repository: 'es.manager.default.product'
    filters:
        multi_dynamic_aggregate:
            attributes:
                request_field: 'attributes'
                name_field: attributes.name
                field: attributes>attributes.value
```  
