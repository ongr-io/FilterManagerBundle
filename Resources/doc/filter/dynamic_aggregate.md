# Dynamic Aggregate Filter

This filter is used to handle nested objects. It aggregates the data from a specified field (`document_field value` 
in configuration) and then groups the results by the values from a separate field (`name_field` in configuration). 
Both fields have to exist in the same nested object of your document. After grouping, several sets of choices are 
made. A single choice from each group can be selected. Results are filtered by the selected choices. 

The functionality of the filter is similar to the Choice Filter, but there are key differences: The Choice Filter 
which creates a single group of choices, however in the Dynamic Aggregate Filter the number and values of the groups 
is dynamic and depend on the data in your documents. This means that the filter is very flexible when dealing with 
certain types of nested objects.

### Dynamic Aggregate filter specific options 

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `name_field`           | Specifies the field in the repository that the results will be grouped by            |
| `show_zero_choices`    | If set to true enables the display of choices with zero available documents.         |

> Important note! Here `name_field` and `document_field` (from general configuration) values should both point to 
the fields in the nested object of the document, however the `document_field` must specify both the `path` and the
`field` properties separated by the `>` sign (e.g. `field = 'attributes>attributes.value'`).

### Configuration example example:
  
```yaml
  
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        # ...
    filters:
        dynamic_attributes:
            type: dynamic_aggregate
            request_field: attributes
            document_field: attributes>attributes.value
            options:
                name_field: attributes.name
                show_zero_choices: false
```  

### Query composition

Lets say we have data in elasticsearch type `product`. Imagine that your products
have such nested objects in their `attributes` field:

```json
[
  {
    "name": "Color",
    "value": "Red"
  },
  {
    "name": "Material",
    "value": "Wood"
  },
  {
    "name": "Made in",
    "value": "Portugal"
  }
]
```

The filter creates a complex set of aggregations for the formation of specific groups
of choices, but the query part is quite strait forward and it simply filters out the
documents by the values provided in the request query. If we had the configuration 
specified above and requested `http://127.0.0.1?attributes[Color]=Red`, we would get 
a query like this:

```json
{
    "post_filter": {
        "nested": {
            "path": "attributes",
            "query": {
                "bool": {
                    "must": [
                        {
                            "term": {
                                "attributes.value": "Red"
                            }
                        },
                        {
                            "term": {
                                "attributes.name": "Colors"
                            }
                        }
                    ]
                }
            }
        }
    },
    "aggregations": {
        "dynamic_attributes-filter": {
            "filter": {
                "match_all": []
            },
            "aggregations": {
                "Color": {
                    "filter": {
                        "match_all": []
                    },
                    "aggregations": {
                        "dynamic_attributes": {
                            "nested": {
                                "path": "attributes"
                            },
                            "aggregations": {
                                "query": {
                                    "terms": {
                                        "field": "attributes.value",
                                        "size": 0,
                                        "order": {
                                            "_count": "asc"
                                        }
                                    },
                                    "aggregations": {
                                        "name": {
                                            "terms": {
                                                "field": "attributes.name"
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                },
                "all-selected": {
                    "filter": {
                        "nested": {
                            "path": "attributes",
                            "query": {
                                "term": {
                                    "attributes.value": "Red"
                                }
                            }
                        }
                    },
                    "aggregations": {
                        "dynamic_attributes": {
                            "nested": {
                                "path": "attributes"
                            },
                            "aggregations": {
                                "query": {
                                    "terms": {
                                        "field": "attributes.value",
                                        "size": 0,
                                        "order": {
                                            "_count": "asc"
                                        }
                                    },
                                    "aggregations": {
                                        "name": {
                                            "terms": {
                                                "field": "attributes.name"
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
```

> Take a look in the [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics) how to pass `Request` object to 
the controller for execute filtering.

### Usage in the templates

Dynamic Aggregate filter returns `AggregateViewData` as the result set for view data. In addition to the standard view
data, it is a container for a set of `ChoiceAwareViewData` objects that represent each group of choices. These items
can be accessed like so:

```twig

{% for choices in filters.dynamic_attributes.items %}
    {# here choices are used as ChoiceAwareViewData #}
    ...
{% endfor %}

```

More information how to use filters in templates can be found in [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics)

