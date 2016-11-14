# Document Value Filter
 
Before reading about this filter take a look at the [base configuration][1]
which applies for all filters.
 
The primary function of this filter is to form categories or types of documents. Unlike most filters this one
does not need URL parameters, instead, it is used together with [ONGR Router][2]. The filter takes the `document`
instance that is provided with the route, gets a value from a specified field and creates a term query with it.

### Document Value Filter specific options

| Setting name | Meaning                                                                                                               |
|--------------|-----------------------------------------------------------------------------------------------------------------------|
| `field`      | The name of the parameter in the document that is passed with the router. This field provides the value for the query |
 
### Configuration example
 
```yaml
                
ongr_filter_manager:
    #...
    filters:
        category:
            type: document_value
            request_field: document # Use only `document` value, unless you have your own router.
            field: name
            document_field: country
        
```
  
> Important! The `request_field` in this filter **must** be `document`, unless you are using your own router. Unlike in 
most filters, it will not be used in the URL

### Query composition

For this section, imagine you need to list all the cities depending on the country. You would need two types of documents: 
a `Country` and a `City`. You would have to include `Country` in the [ONGR router][2] and configure its controller (how to 
handle the request with Filter Manager in this controller can be found in the [Basics][1] section). After this all you would 
need to do is to go to the URL that matches the `url` field in one of the `Country` documents. Lets imagine that one of 
those documents is this:

```json

{
  "url": "/france",
  "name": "France"
}

```

If this is so and you go to the URL `www.example.com/france`, then the query formed by Document Value filter will be:
 
```json

{
  "post_filter": {
    "term": {
      "country": "France"
    }
  }
}

```

> Note, that the Filter Manager here is provided the repository of the `City`, more on that in the [Basics][1] section.

### Usage in the templates

This filter returns a simple ViewData object, it is not intended to create any view-specific choices. The aim of this 
filter is to limit the result set of the documents.

> For more information on how to handle a request or retrieve data, please refer to the [basics topic][1]

[1]: http://docs.ongr.io/FilterManagerBundle/Basics
[2]: http://docs.ongr.io/RouterBundle