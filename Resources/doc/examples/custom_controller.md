# Usage

## Default controller

Once you set up your *managers* you don’t need to create a controller for each one, you can just use default manager controller ONGRFilterManagerBundle:Manager:manager.

Example:

```yaml
# src/AppBundle/Resources/config/routing.yml

ongr_search_page:
    pattern: /list
    methods:  [GET]
    defaults:
        _controller: ONGRFilterManagerBundle:Manager:manager
        managerName: "item_list"
        template: "AppBundle:List:results.html.twig"
```

This specific example will render template AppBundle:List:results.html.twig, with [SearchResponse] object from filter manager named `filter_manager`.

## Custom controller

You can still use custom controller by getting your needed manager from service container. This way you can add your custom variables if needed.

Example:

```yaml
# src/AppBundle/Resources/config/routing.yml

ongr_search_page:
    pattern: /list
    methods:  [GET]
    defaults:
        _controller: AppBundle:List:index
```

```php
# src/AppBundle/Controller/ListController.php

/**
 * Controller for list pages.
 */
class ListController extends Controller
{
    /**
     * Renders my list page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $results = $this
            ->get(ONGRFilterManagerExtension::getFilterManagerId('item_list'))
            ->handleRequest($request);

        return $this->render(
            'AppBundle:List:results.html.twig',
            [
                'filter_manager' => $results,
                'my_custom_variable' => $var,
            ]
        );
    }
}
```

## Template variables

If you’re using default controller, [SearchResponse] from
[FilterManager](https://github.com/ongr-io/FilterManagerBundle/blob/master/Search/FilterManager.php) will be named 
`filter_manager` in template, otherwise it’s whatever you call it in your controller.

You can use [SearchResponse] to get results in your template:

```twig
{% for item in filter_manager.result %}
    <b>{{ item.title }}</b>
{% endfor %}
```

You can also use it to get data about your filter:

```twig
Pager url parameters: {{ filter_manager.filters.pager.urlParameters }}
```

A complete list of parameters for each filter can be found can be found
on [View data page](http://docs.ongr.io/FilterManagerBundle/ViewData).

[SearchResponse]: https://github.com/ongr-io/FilterManagerBundle/blob/master/Search/SearchResponse.php
