# Match Filter

Before reading about this filter take a look at the [base configuration](http://docs.ongr.io/FilterManagerBundle/Basics) 
which applies for all filters.

This filter is used for search. This filter doesn't form any lists of choices, it limits results to match the request. 

### Match filter specific document_field setting

Formation of `document_field` setting:
 * `,` - separates fields, this way several different fields can be provided.
 * `^` - applies boosting for a field, if followed by a number (e.g. `field^2`).
 * `>` - separates `path` and `field` if provided field is in a nested object.
 
 > All of the operators described above can be applied to the same `document_field` setting
 
Example of a complex `document_field` value for the filter:

```yaml
document_field: title^3,variants>variants.color,description^2
```

In an example above, the search will be done on three fields: title, description and
color of a nested variants object. Also, title and description fields will be boosted
by 3 and 2 accordingly.

### Match filter specific options

Match filter uses Elasiticsearch [Match Query][1] in its core and all the specific options are passed to it.
This means that the specific options of the *Match Filter* are the same as the options of the *Match Query*. 
Here are a few of the more commonly used ones:

| Setting name           | Meaning                                                                                        |
|------------------------|------------------------------------------------------------------------------------------------|
| `fuzziness`            | Enables inexact matching of the results. Expects an integer value                              |
| `prefix_length`        | Used together with `fuzziness`. Determines the length of the prefix that won't change          |
| `operator`             | Controls the clauses of the inner boolean query. Can be set to `or` or `and`, defaults to `or` |

You can read more about them or get the complete list in the [official docs][1]
  
### Configuration example example:
  
```yaml
# app/config/config.yml
ongr_filter_manager:
    #...
    filters:
        search:
            type: match
            request_field: q
            document_field: title,description^2,attributes>attributes.title^3
            options:
                operator: and
                fuzziness: 2

```
### Query composition

According to the specified configuration, the filter will become active when the request query will contain `q` parameter. 
Therefore, if the request being executed is `http://127.0.0.1?q=example`, the filter will execute this query: 

```json
{
    "query": {
        "bool": {
            "should": [
                {
                    "match": {
                        "title": {
                            "query": "example",
                            "fuzziness": 2,
                            "operator": "and"
                        }
                    }
                },
                {
                    "match": {
                        "description": {
                            "query": "example",
                            "fuzziness": 2,
                            "operator": "and",
                            "boost": "2"
                        }
                    }
                },
                {
                    "nested": {
                        "path": "attributes",
                        "query": {
                            "match": {
                                "attributes.title": {
                                    "query": "example",
                                    "fuzziness": 2,
                                    "operator": "and",
                                    "boost": "3"
                                }
                            }
                        }
                    }
                }
            ]
        }
    }
}
```

To start using the filter, simply call the `handleRequest` method of the filter manager from the
controller. After the request is handled, only documents that contain `experiment` (or any very similar
word, due to fuzziness) will appear in the results.
  
### Usage in the templates

This filter returns a simple `ViewData` object, it is not intended to create any view-specific choices.
The aim of this filter is to limit the result set of the documents.

> For more information on how to handle a request or retrieve data, please refer to the [basics topic](http://docs.ongr.io/FilterManagerBundle/Basics)

[1]: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html