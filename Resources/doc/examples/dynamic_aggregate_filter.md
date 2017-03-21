# Dynamic Aggregate Filter  

Here we provide a full example of how to implement *dynamic aggregate filter*.

  
## 1. Create a document  
 
We will create a `Product` document with a nested `attributes` object that the
filter will be applied on.

```php
// src/AppBundle/Document/Product.php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Collection\Collection;

/**
 * @ES\Document(type="product")
 */
class Product
{
    /**
     * @var string
     *
     * @ES\Id()
     */
    public $id;

    /**
     * @ES\Property(type="keyword")
     */
    public $title;

    /**
     * @var Attribute[]
     * @ES\Embedded(class="AppBundle:Attribute", multiple=true)
     */
    public $attributes;

    public function __construct()
    {
        $this->attributes = new Collection();
    }
}
```  

And now the `Attribute` object class:

```php
// src/AppBundle/Document/Attribute.php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Nested()
 */
class Attribute
{
    /**
     * @var string
     * @ES\Property(type="keyword", options={"index":"not_analyzed"})
     */
    public $name;

    /**
     * @var string
     * @ES\Property(type="keyword", options={"index":"not_analyzed"})
     */
    public $value;
}
```

> Note that internally `DynamicAggregateFilter` uses the `TermQuery`, which
means that if the index will be analyzed you may get unexpected results with it
or it may not work at all. If you need the properties analyzed for other search operations,
you can use [multi field annotations](http://docs.ongr.io/ElasticsearchBundle/mapping)

## 3. Adding filter to manager

Adding filter to the manager is a standard procedure as with all the filters:

```yaml
# app/config/config.yml

ongr_filter_manager:
    managers:
        attribute_list:
            filters:
                - attributes
            repository: 'es.manager.default.product'
    filters:
        attributes:
            type: dynamic_aggregate
            request_field: 'attributes'
            document_field: attributes>attributes.value
            options:
                name_field: attributes.name
```

## 4. Using filter  

As with any other filter, in order to use it, the request needs to be 
handled in the controller:

```php
/**
 * Main action for lists
 *
 * @param Request $request
 *
 * @return Response
 */
public function listAction(Request $request)
{
    $filterManager = $this->get('ongr_filter_manager.attribute_list')->handleRequest($request);

    return $this->render('default/list.html.twig', [
        'filter_manager' => $filterManager,
    ]);
}
```

The resulting documents will be filtered depending on the request and
the formation of the available choices can be made like this:

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