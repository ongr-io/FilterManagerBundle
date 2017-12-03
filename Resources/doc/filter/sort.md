# Sort Filter

Before reading about this filter take a look at the [base configuration](http://docs.ongr.io/FilterManagerBundle/Basics) which applies for all filters.

This filter is used for sorting the documents.

### Sort filter specific options

| Setting name           | Meaning                                                            |
|------------------------|--------------------------------------------------------------------|
| `choices`              | Associative array that defines all available sorting possibilities |

Formation of `choices` array:

Each choice in `choices` array is a way to sort data. `choices` is an associative array, where
keys identify unique ways of sorting data. This key will also appear in the url once a sorting 
choice is selected (e.g. `example.com?sort=price_desc` where `price_desc` is the key of a choice).
 
Formation of each choice:

Each choice accepts several parameters:

| Setting name           | Meaning                                                            |
|------------------------|--------------------------------------------------------------------|
| `field`                | Specifies the field in repository to sort on. (e.g. `item_color`)  |
| `order`                | Order to sort by. Default `asc`. Valid values: `asc`,  `desc`.     |
| `default`              | Specifies whether this choice is the default one. Default `false`. |
| `mode`                 | Interprets the value of sorting when defined field is an array. For any arrays: `min`, `max`, for numeric arrays `avg`, `sum`.     |
| `fields`               | Used when sorting on several fields at once. More information below |

The main parameters here are `field`, `order` and `mode`. If there is a need to sort on several
fields, you can specify the `fields` parameter, which is just a set of the three parameters specified
before.

`default` should only be set to true in one choice.

> Since this filter works with several ES fields, depending on the request, the 
`document_field` value should be set to null (`~`).

### Configuration example:

```yaml
# app/config/config.yml
    
ongr_filter_manager:
    # ...
    filters:
        search_sort:
            type: sort
            request_field: sort
            document_field: ~
            options:
                choices:
                    # single field sorting
                    stock_asc: { field: stock, default: true, order: asc }
                    stock_desc: { field: stock, order: desc }
                    # molti field sorting
                    price_desc_hits_desc: 
                        fields: 
                            - {field: price, order: desc}
                            - {field: hits, order: desc}
                    price_desc_hits_asc:
                        fields:
                            - {field: price, order: desc}
                            - {field: hits, order: asc}

```

### Query composition

Considering the configuration above, if we execute a request `http://127.0.0.1?sort=price_desc_hits_asc`
The following will be added to a search:

```json

{
    "sort": [
        {
            "price": {
                "order": "desc"
            }
        },
        {
            "hits": {
                "order": "asc"
            }
        }
    ]
}

```

Because a default value is specified, request query that does not contain `sort`
parameter will have its results sorted according to stock ascending.

> Take a look in the [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics) how to pass `Request` object to the controller for execute filtering. 

### Usage in the templates

Sort filter returns `ChoiceAwareViewData` as the result set for view data. 
Each choice is a possible option for sorting data and the usage of it is similar
to the one from the *Choice* filter.

Here is an example of how to create a list of options for sorting in twig environment:

```twig
{% set sortFilter = filters.search_sort %}
<ul>
    {% for choice in sortFilter.choices %}
        <li>
            {% if choice.active %}
                <a href="{{ path('your_route', choice.unsetUrlParameters) }}" class="active">
                    {{ choice.label|trans() }}
                </a>
            {% else %}
                <a href="{{ path('your_route', choice.urlParameters) }}">
                    {{ choice.label|trans() }}
                </a>
            {%  endif %}
        </li>
    {% endfor %}
</ul>
```

Notice here that the label of each choice is the key specified in the configuration.
We highly recommend including your keys in [Symfony translations][1] to give them nice, 
descriptive names.

[1]: http://symfony.com/doc/current/translation.html 
