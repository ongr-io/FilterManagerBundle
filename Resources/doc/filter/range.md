# Range Filter

This filter allows filtering result set by given range of values.

## Configuration

| Setting name    | Meaning                                                                           |
|-----------------|-----------------------------------------------------------------------------------|
| `request_field` | Request field used to specify filter value. (e.g. `www.page.com/?request_field=4`)|
| `field`         | Specifies the field in repository to apply this filter on. (e.g. `item_color`)    |
| `inclusive`     | If set to true include edge values to result set. Defaults to `false`.            |
| `tags`          | Array of filter specific tags that will be accessible at Twig view data.          |
  
Example:
  
```yaml
# app/config/config.yml
    
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - weight
            repository: 'es.manager.default.product'
    filters:
        range:
            weight:
                request_field: 'weight'
                field: weight
                inclusive: true
```

When using this filter, `request_field` GET parameters need to be provided as two numbers separated 
by `;`. For example, to search for values between 10 and 20 you would need to provide a URL like this:
`www.page.com/?request_field=10;20`. If there is no specific value, that is, all the values that are
 grater than 100 need to be provided, `-` must be specified in stead of a number:
`www.page.com/?request_field=100;-`.

## Twig view data

View data returned by this filter to be used in template:
 
| Method                  | Value                                            | 
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getMinBounds()          | Minimal value in all documents in provided field |
| getMaxBounds()          | Maximal value in all documents in provided field |
| getTags()               | Lists all tags specified at filter configuration |
| hasTag($tag)            | Checks if filter has the specific tag            |
