# Field value filter

Purpose of this filter is to select only those documents which have predefined value in predefined field. Sample use case could be to display only active items (having true in field `active`).

> Note that this filter does not depend from request and is always active.

## Configuration 

| Setting name | Meaning                                                                     |
|--------------|-----------------------------------------------------------------------------|
| `field`      | Specifies the field in repository to apply this filter on. (e.g. `active`)  |
| `value`      | Specifies exact value which should be in `field`.                           |
| `tags`       | Array of filter specific tags that will be accessible at Twig view data.    |

Example:
  
```yaml
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - only_active
            repository: 'es.manager.default.product'
    filters:
        field_value:
            only_active:
                field: 'active'
                value: true
```  

## Twig view data

This filter is not included in view data.