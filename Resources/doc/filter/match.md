# Match Filter

This filter searches for a matching value in the specified field. Usual use case is search functionality.

## Configuration

| Setting name           | Meaning                                                                                        |
|------------------------|------------------------------------------------------------------------------------------------|
| `request_field`        | Request field used to specify filter value. (e.g. `www.page.com/?request_field=4`)             |
| `field`                | Specifies the field in repository to apply this filter on. (e.g. `item_color`)                 |
| `fuzziness`            | Enables inexact matching of the results.                                                       |
| `operator`             | Controls the clauses of the inner boolean query. Can be set to `or` or `and`, defaults to `or` |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.                       |
  
Example of the configuration:
  
```yaml
# app/config/config.yml
    
ongr_filter_manager:
    managers:
        search_list:
            filters:
                - search
            repository: 'es.manager.default.product'
    filters:
        match:
            search:
                request_field: 'q'
                field: title
                operator: and
                fuzziness: 2

```
It also has to be mentioned that more than one field can be specified. If you want to apply the filter
to more than one field you can specify it by separating them with commas like so:

```yaml
....
#    search:
#        request_field: 'q'
#        field: title, description
#    search-with-heights:
#        request_field: 'q'
#        field: title^3, description^2, longdesc
....
```
In the example above ^ sign denotes the boost that is assigned to a specific field

## Twig view data

View data returned by this filter to be used in template:
 
| Method                  | Value                                            | 
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getTags()               | Lists all tags specified at filter configuration |
| hasTag($tag)            | Checks if filter has the specific tag            |
  

## Usage in template example

This example uses filter defined in [Search example](../examples/search_example.md). To display this filter we would add following code to template:

```twig
<form action="{{ path(app.request.attributes.get('_route')) }}" method="get">
    <input name="q" placeholder="Search..." value="{{ filter_manager.getFilters().search.getState().getValue() }}">
    <input type="submit" value="Search">
</form>
```
