# Document Field Filter  

This filter gets a document from request and filters results by its id on the specified field.
This is very useful if you want to get documents by a category or any other similar case.
  
If you are using [ONGR Router bundle](https://github.com/ongr-io/RouterBundle) you know that it adds document in Symfony's `Request` object. This filter uses document resolved by ONGR Router bundle.

## Configuration  
 
| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to specify filter value. (e.g. `www.page.com/?request_field=3`)   |
| `field`                | Specifies the field in repository to apply this filter on. (e.g. `category_id`)      |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |
  
Example:
  
```yaml
# app/config/config.yml

ongr_filter_manager:
    managers:
        item_list:
            filters:
                - colors
            repository: 'es.manager.default.item'
    filters:
        document_field:
            colors:
                request_field: 'category'
                field: category_id
```  

In this case when we are in category page only items with category_id equal to current category's id will be added to results list. Full working example can be found in [using document_field filter](../examples/using_document_field_filter.md).

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
