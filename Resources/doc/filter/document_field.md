# Document field filter.

This filter gets a document from request and filters results by its id on the specified field.
This is very useful if you want to get documents by a category.

For example, lets say we have `item` repository which contains the following data:

| color | category_id |
|-------|-------------|
| red   | 1           |
| red   | 3           |
| blue  | 2           |
| green | 4           |
| blue  | 1           |

Lets say we apply *document filter* and send a request with a category item, which has id value of 3, we will get:

| color | category_id |
|-------|-------------|
| red   | 3           |

# Configuration

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to specify filter value. (f.e. `www.page.com/?request_field=3`)   |
| `field`                | Specifies the field in repository to apply this filter on. (f.e. `category_id`)      |

Example:
```yaml
#app/config/config.yml
ongr_filter_manager:
    managers:
        item_list:
            filters:
                - colors
            repository: 'item'
    filters:
        document_field:
            colors:
                request_field: 'category'
                field: category_id
```

# Twig view data

View data returned by this filter to be used in template:

| Method                  | Value                                            |
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
