# Field Value Filter

Purpose of this filter is to select only those documents which have predefined value in a predefined field. 
Usual use case could be to display only active items (having true in field `active`). In reality this is
the most simple filter to use.

> Note that this filter does not depend on request and is always active.

### Document Value Filter specific options

| Setting name | Meaning                                                                     |
|--------------|-----------------------------------------------------------------------------|
| `value`      | Specifies exact value which should be in `document_field`.                  |

>Note that this filter does not depend on requests, so `requst_field` should be set to `~`


### Configuration example
  
```yaml
# app/config/config.yml
  
ongr_filter_manager:
    #...
    filters:
        only_active:
            type: field_value
            request_field: ~
            document_field: 'active'
            options:
                value: true
```  

### Query composition

As mentioned before, this is a very simple filter and, given the configuration above, it will
always create the same query:
 
```json

{
  "post_filter": {
    "term": {
      "active": true
    }
  }
}

```

### Usage in the templates

This filter returns a simple ViewData object, it is not intended to create any view-specific choices. The aim of this 
filter is to limit the result set of the documents.
 
> For more information on how to handle a request or retrieve data, please refer to the [basics topic][http://docs.ongr.io/FilterManagerBundle/Basics]