# Choice Filter

Before reading about this filter take a look at the [base configuration](http://docs.ongr.io/FilterManagerBundle/Basics) which applies for all filters.

This filter groups values of repository in a specified field, and returns available options.
If you select one of the options, *choice filter* will return item list filtered by it.

### Choice filter specific options

| Setting name           | Meaning                                                                                 |
|------------------------|-----------------------------------------------------------------------------------------|
| `size`                 | Specifies the filter choices amount.                       |
| `show_zero_choices`    | Includes choices that have 0 documents in the choice array (defaults to `false`)        |
  
### Configuration example example:

```yaml
# app/config/config.yml
ongr_filter_manager:
    #...
    filters:
        color:
            type: choice
            request_field: color
            document_field: color
            options:
                size: 10 # <- there will be maximum 10 aggregated filter options.
    #...
```

### Query composition

Lets say we have a data in elasticsearch in `product` type and filter configuration from example above.

```json
[
  {
    "_id": "1",
    "color": "black",
    "title": "Rockyard"
  },
  {
    "_id": "2",
    "color": "black",
    "title": "Endipine"
  },
  {
    "_id": "3",
    "color": "red",
    "title": "Pearlessa"
  }
]
```

According to the configuration, the filter will become active when there will be `color` in the request query.

If the state is inactive it will just collect the options by executing query:

```json
{
    "aggregations": {
        "color": {
            "terms": {
                "field": "color",
                "order": {
                    "_count": "asc"
                },
                "size": 10
            }
        }
    }
}
```

So lets say we execute request with `http://127.0.0.1?color=black`. In this particular case filter state becomes active and executes query:

```json
{
    "post_filter": {
        "term": {
            "color": "black"
        }
    },
    "aggregations": {
        "color": {
            "terms": {
                "field": "color",
                "order": {
                    "_count": "asc"
                },
                "size": 10
            }
        }
    }
}
```

> Take a look in the [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics) how to pass `Request` object to the controller for execute filtering. 

### Usage in the templates

`choice` returns `ChoiceAwareViewData` as the result set for view data. Check the available functions in this class.

More information how to use filters in templates can be found in [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics)

