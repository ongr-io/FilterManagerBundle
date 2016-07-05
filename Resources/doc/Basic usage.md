# Basic Usage

In this chapter we want to go over the basic usage of the most popular filters in detail.
For the purposes of this chapter we will consider that we have a defined `product_list` filter manager
that holds multi_choice filter `color`, and a pager filter `pager`. This example application will
look at creating a paginated list of products, that can be filtered by color attribute of the products.

The controller would simply look like this:

```php

    public function listAction(Request $request)
    {
        $this->setLocalesToFields($request);
        $filterManager = $this->get('ongr_filter_manager.product_list')->handleRequest($request);

        return $this->render(
            'product/list.html.twig',
            [
                'filter_manager' => $filterManager,
            ]
        );
    }

```

As you can see, only one line of code is required to fully handle such request. Here, after handling the
request, filter manager already holds the right amount of products to fit in the page that have a correct
offset, if the pagers request parameter is set, and the products are filtered by color if color parameter
is set to the request. Make sure to pass the filter manager as a parameter to the twig template.

The next thing to do is to render the list in twig. Firstly, in a sidebar a list of colors with the number
of products that have a certain color will be rendered:

```twig

<ul>
{% for choice in filter_manager.getFilters().color.getChoices() %}
    <li>
        <a href="{{ path(app.request.attributes.get('_route'), choice.getUrlParameters()) }}">
            {{ choice.getLabel() }} ({{ choice.getCount() }})
        </a>
    </li>
{% endfor %}
</ul>

```

Here every choice of the color filter will generate a link to the same route, but with additional GET
parameter that corresponds to that choice. It will also generate a choice title and the number of products
that have that particular choice. An example of it could be `Green (12)` or `Blue (5)`.

Next the product list itself needs to be rendered in the main section of the page, like so:

```twig

{% for product in filter_manager.getResult %}
    <div class="col-md-3 col-sm-6 col-xs-6 product-container">
        <div class="thumbnail product-thumbnail">
            {% set product_url = path('ongr_route', { 'document': product }) %}
            <img src="{{ product_image(product) }}" class="product-image" alt="Product image"/>
        </div>
        <h5 class="product-title">
            <a class="product-link" href="{{ product_url }}">product.title</a>
        </h5>
        <strong class="product-price"> product.price </strong>
    </div>
{% endfor %}

```

Lastly, pagination is added to the bottom of the main section:

```twig

{% if filter_manager.getFilters().pager is defined %}
    <div class="list-pagination clearfix">
        {% set parameters = app.request.query.all %}
        <div class="pull-right">{{ ongr_paginate(filters.pager.pager, 'ongr_route', parameters) }}</div>
    </div>
{% endif %}

```

And that's it, minimal effort for maximum effect, of course, you can do so much more and for more information
on how to add and use other filters please refer to the sections dedicated to individual filters and
maximize the possibilities the ONGR Filter Manager provides.