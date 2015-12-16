# Sort Filter

Filter used for sorting the documents.

## Configuration

First, you have to specify the request field:

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to pass the sort choice id (e.g. `www.page.com/?request_field=4`) |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |

After which you can specify multiple sort options/choices:

| Setting name           | Meaning                                                            |
|------------------------|--------------------------------------------------------------------|
| `label`                | Choice name to be used in templates. (e.g. `Title descending`)     |
| `field`                | Specifies the field in repository to sort on. (e.g. `item_color`)  |
| `order`                | Order to sort by. Default `asc`. Valid values: `asc`,  `desc`.     |
| `default`              | Specifies whether this choice is the default one. Default `false`. |
| `mode`                 | For any arrays: `min`, `max`, for numeric arrays `avg`, `sum`.     |
| `fields`               | Array of fields to sort on. For more information see table below.  |

> `field`, `order`, and `mode` are ignored if at least one of fields is defined.

Each object in `fields` array specifies sorting condition. Available parameters are defined below:

| Setting name           | Meaning                                                            |
|------------------------|--------------------------------------------------------------------|
| `field`                | Specifies the field in repository to sort on. (e.g. `item_color`)  |
| `order`                | Order to sort by. Default `asc`. Valid values: `asc`,  `desc`.     |
| `mode`                 | For any arrays: `min`, `max`, for numeric arrays `avg`, `sum`.     |

Example:

```yaml
# app/config/config.yml
    
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - search_sort
            repository: 'es.manager.default.product'
    filters:
        sort:
            search_sort:
                request_field: 'sort'
                choices:
                  - { label: No sorting, key: score, field: _score, default: true }
                  - { label: Heaviest to lightest, key: weight_desc, field: weight, order: desc }
                  - { label: Lightest to heaviest, key: weight_asc, field: weight, order: asc  }

```

## Twig view data

View data returned by this filter to be used in template:

| Method                  | Value                                            |
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getChoices()            | Returns a list of available sort choices         |
| getTags()               | Lists all tags specified at filter configuration |
| hasTag($tag)            | Checks if filter has the specific tag            |


Each choice has its own data:

| Method             | Value                                      |
|--------------------|--------------------------------------------|
| isActive()         | Is this choice currently applied           |
| isDefault()        | Is this choice the default one             |
| getLabel()         | Choice label                               |
| getUrlParameters() | Returns a list of available choices        |
| getMode()          | Returns a mode value if is set             |

## Usage in template example

This example uses filter defined in [Search example](../examples/search_example.md). To display this filter we would add following code to template:

```twig
{% set sortFilter = filter_manager.getFilters().search_sort %}
<ul>
    {% for choice in sortFilter.getChoices() %}
        <li>
            {% if choice.isActive() %}
                <b>{{ choice.getLabel() }}</b>
            {% else %}
                <a href="{{ path(app.request.attributes.get('_route'), choice.getUrlParameters()) }}">{{ choice.getLabel() }}</a>
            {%  endif %}
        </li>
    {% endfor %}
</ul>
```
