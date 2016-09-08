# Dynamic Aggregate Filter

This filter is used to aggregate the data provided from a specific field
in a nested object of the document and then group the results by the values 
from a different field of that same nested object. This means that the 
results are grouped dynamically, without the need to specify exact names for
grouping. Other than that, the functionality of the filter bears similarities 
to the *choice filter*.

## Configuration 

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to view the selected page. (e.g. `www.page.com/?request_field=4`) |
| `name_field`           | Specifies the field in the repository that the results will be grouped by            |
| `field`                | Specifies the field in repository to apply this filter on.                           |
| `sort`                 | Sorts the choices based on your configuration.                                       |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |

> Important note! Here `name_field` and `field` values should both point to the fields in the nested object of the
document, however the `field` must specify both the `path` and the `field` properties separated by the `>` sign 
(e.g. `field = 'attributes>attributes.value'`).

Example:
  
```yaml
  
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - attributes
            repository: 'es.manager.default.product'
    filters:
        dynamic_aggregate:
            attributes:
                request_field: 'attributes'
                name_field: attributes.name
                field: attributes>attributes.value
```  

## Twig view data

Twig view data returned by this filter is an instance of `AggregateViewData` class.
This object has an array of `ChoiceAwareViewData` that can be accessed through
`getItems()` method. Once accessed it provides these methods to retrieve the
information stored within:

| Method                  | Value                                            |
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getChoices()            | Returns a list of available choices              |
| getTags()               | Lists all tags specified at filter configuration |
| hasTag($tag)            | Checks if filter has the specific tag            |

Each choice has its own data:

| Method             | Value                                      |
|--------------------|--------------------------------------------|
| isActive()         | Is this choice currently applied           |
| isDefault()        | Is this choice the default one             |
| getCount()         | Return the number of items for this choice |
| getLabel()         | Choice label                               |
| getUrlParameters() | Returns a list of available choices        |

## Usage in template example

```twig
<ul>
    {% for choices in filter_manager.getFilters().getItems() %}
        <li>
            <a href="#"><strong>{{ choices.name }}</strong></a>
        </li>
        {% for choice in choices.choices %}
            <li>
            {% if choice.active %}
                <a href="{{ path(active_category, choice.getUrlParameters()) }}"
                   class="list-group-item active">{{ choice.label }}({{ choice.count }})</a>
            {% else %}
                <a href="{{ path(active_category, choice.getUrlParameters()) }}"
                   class="list-group-item">{{ choice.label }} ({{ choice.count }})</a>
            {% endif %}
            </li>
        {% endfor %}
    {% endfor %}
</ul>
```

The example above would render out a list of available choices of documents with their 
corresponding amounts and they would be grouped by the names that would be generated from
the provided `name_field` of the same nested object.