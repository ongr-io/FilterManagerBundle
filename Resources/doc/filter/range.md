# Range Filter

Before reading about this filter take a look at the [base configuration](http://docs.ongr.io/FilterManagerBundle/Basics) which applies for all filters.

This filter allows filtering result set by given range of values. It also provides the minimum
and maximum available values.

### Range Filter Specific Options

| Setting name    | Meaning                                                                           |
|-----------------|-----------------------------------------------------------------------------------|
| `inclusive`     | If set to true include edge values to result set. Defaults to `false`.            |
  
### Configuration Example
  
```yaml
# app/config/config.yml
    
ongr_filter_manager:
    # ...
    filters:
        weight:
            type: range
            request_field: weight
            document_field: weight
            options:
                inclusive: true
```

### Query Composition

If the configuration is as described above, the filter will become active only if
there will be `weight` value in the request query. If there is no such value, it 
will simply collect some statistics form the field with the following aggregation:

```json

{
    "aggregations": {
        "weight": {
            "stats": {
                "field": "wight"
            }
        }
    }
}

```

So lets say we execute request with `http://127.0.0.1?weight=13;56`. Notice that there
are two variables separated by `;`. This indicates the range and it is the correct 
format for the request. If the request will not contain `;` sign, the filter will become
inactive. In this particular case though, filter state becomes active and executes 
the following query:

```json

{
    "post_filter": {
        "range": {
            "weight": {
                "gte": 13,
                "lte": 56
            }
        }
    },
    "aggregations": {
        "weight": {
            "stats": {
                "field": "weight"
            }
        }
    }
}

```

Should be mentioned, that the `gte` and `lte` are used in stead of `gt` and `lt` 
because the `inclusive` setting is set to `true` in configuration.

> Take a look in the [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics) how to pass `Request` object to the controller for filter execution.

### Usage in the templates

`Range` returns `RangeAwareViewData` as the result set for view data. Check the available functions in this class.

`RangeAwareViewData` specific methods:
 
| Method                  | Value                                               | 
|-------------------------|-----------------------------------------------------|
| getMinBounds()          | Returns minimal value form the current document set |
| getMaxBounds()          | Returns maximal value form the current document set |

To use this filter in twig you have to provide a way to add the range to the request query.
The methods from `RangeAwareViewData` help you to figure out the range that your current data
set has, but the range itself needs to be specified by manually. The example below shows an
example of how you can define a list of weight ranges.

```twig

<ul>
    {% set filter = filter_manager.filters.weight %}
    <li>
        <a href="{{ path('your_route', filter.urlParameters|merge("weight":"0;10") }}">0 - 10 kg</a>
    </li>
    <li>
        <a href="{{ path('your_route', filter.urlParameters|merge("weight":"10;25") }}">10 - 25 kg</a>
    </li>
    <li>
        <a href="{{ path('your_route', filter.urlParameters|merge("weight":"25;50") }}">25 - 50 kg</a>
    </li>
    <li>
        <a href="{{ path('your_route', filter.resetUrlParameters }}"> All </a>
    </li>
</ul>

```