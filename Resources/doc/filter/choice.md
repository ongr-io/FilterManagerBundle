# Choice Filter

This filter groups values of repository in a specified field, and returns available options.
If you select one of the options, *choice filter* will return item list filtered by it.

## Configuration 

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to view the selected page. (e.g. `www.page.com/?request_field=4`) |
| `field`                | Specifies the field in repository to apply this filter on. (e.g. `item_color`)       |
| `sort`                 | Sorts the choices based on your configuration.                                       |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |
  
Sorting configuration  
---------------------  

| Setting name           | Meaning                                                                                 |
|------------------------|-----------------------------------------------------------------------------------------|
| `type`                 | You can sort either by the `_term` which was aggregated or by the `_count` of the terms.|
| `order`                | Specifies the ordering direction. Either ascending or descending.                       |
| `priority`             | Highest priority term names, the first terms to be shown in choices list.               |
  
Example:
  
```yaml
  
# app/config/config.yml
  
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - color
            repository: 'es.manager.default.product'
    filters:
        choice:
            color:
                request_field: 'color'
                field: color
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
<ul>
{% for choice in filter_manager.getFilters().color.getChoices() %}
    <li>
        <a href="{{ path(app.request.attributes.get('_route'), choice.getUrlParameters()) }}">{{ choice.getLabel() }}</a> ({{ choice.getCount() }})
    </li>
{% endfor %}
</ul>
```
