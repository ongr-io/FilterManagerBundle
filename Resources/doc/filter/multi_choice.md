Multiple Choice Filter  
======================  
This filter is very similar to choice filter, but you can select multiple options.
It groups values of a repository in a specified field and returns available options.
If you select one or more of the options, *multi choice filter* will return item list filtered by the selected options.
  
## Configuration

| Setting name           | Meaning                                                                                          |
|------------------------|--------------------------------------------------------------------------------------------------|
| `request_field`        | Request field used to view the selected page. (e.g. `www.page.com/?request_field=4`)             |
| `field`                | Specifies the field in repository to apply this filter on. (e.g. `item_color`)                   |
| `sort`                 | Choices can also be sorted. You can read more about this [here](choice.md#sorting-configuration).|
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.                         |
  
Example:
  
```yaml
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - country
            repository: 'es.manager.default.product'
    filters:
        multi_choice:
            country:
                request_field: 'country'
                field: country
``` 

## Twig view data

View data returned by this filter to be used in template:

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

This example uses filter defined in [Search example](../examples/search_example.md). To display this filter we would add following code to template:

```twig
{% set choiceFilter = filter_manager.getFilters().color %}
<ul>
    {% for choice in choiceFilter.getChoices() %}
        <li>
            {% if choice.isActive() %}<b>{% endif %}
                <a href="{{ path(app.request.attributes.get('_route'), choice.getUrlParameters()) }}">
                    {{ choice.getLabel() }}</a>
                ({{ choice.getCount() }})
            {% if choice.isActive() %}</b>{% endif %}
        </li>
    {% endfor %}
</ul>
{% if choiceFilter.getState().isActive() %}
    <a href="{{ path(app.request.attributes.get('_route'), choiceFilter.getResetUrlParameters()) }}">
        Deactivate this filter
    </a>
{% endif %}
```