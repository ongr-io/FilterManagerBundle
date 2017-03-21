# Custom Filter  

There is possibility to add custom filters to filter managers via tagged filter service.
You must create filter class, define it as a service with `ongr_filter_manager.filter` tag.
Afterwards it will be available to use the same way that the regular filters are.
  
## 1. Create filter class  
 
The only real requirement for the class must implement [`FilterInterface`](https://github.com/ongr-io/FilterManagerBundle/blob/master/Filter/FilterInterface.php),
But our recommendation is to extend one of the filters to gain the base functionality. 

In this example we will provide a filter that will exclude documents with certain values in specified field:

```php
<?php

namespace AppBundle\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\Widget\Search\DocumentValue;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
* Filter for ONGR FilterManager. Excludes certain values from search.
*/
class ExclusionFilter extends DocumentValue
{
    /**
    * {@inheritdoc}
    */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $exclusion = new BoolQuery();

        if (!empty($values = $this->getOption('exclude'))) {
            foreach ($values as $value) {
                $exclusion->add(new TermQuery($this->getDocumentField(), $value), BoolQuery::MUST_NOT);
            }
        }

        $search->addQuery($exclusion);
    }

    /**
    * {@inheritdoc}
    */
    public function preProcessSearch(Search $search, Search $relatedSearch, FilterState $state = null)
    {
        // Nothing more to do here.
    }
}
``` 
 
As you can see, the filter will exclude all the values that will be defined in the `exclude` option
from the results.
  
## 2. Defining service  

Filter service must be tagged with `ongr_filter_manager.filter` tag, and you must also provide a unique type name
that will be used when you will be defining the actual filters.
  
```yaml
# app/config/services.yml

services:
    app.filter:
        class: AppBundle\Filter\ExclusionFilter
        tags:
            - { name: ongr_filter_manager.filter, type: app.exclusion_filter }
```

## 3. Adding filter to manager

You can add custom filter in the same way that you add regular filters. The only catch here is that you
need to provide your newly created filter type

```yaml
# app/config/config.yml

ongr_filter_manager:
    managers:
        search_list:
            filters:
                - custom_exclusion_filter
                # ...
            repository: 'es.manager.default.product'
    filters:
        # ...
        custom_exclusion_filter:
            type: app.exclusion_filter #notice that we are using our custom filter type here
            request_field: ~
            document_field: category
            options:
                exclude: ['hidden', 'private', 'classified']
```

## 4. Using filter  

Filter can be used as other filters through `FilterManager`, see [`basics chapter`](http://docs.ongr.io/FilterManagerBundle/Basics).
