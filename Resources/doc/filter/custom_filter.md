# Custom Filter  

There is possibility to add custom filters to filter managers via tagged filter service.
You must create filter class, define it as a service with `ongr_filter_manager.filter` tag.
  
## 1. Create filter class  
 
Class must implement [`FilterInterface`](https://github.com/ongr-io/FilterManagerBundle/blob/master/Filters/FilterInterface.php).
This class will boost some fields depending on values.

```php
// src/AppBundle/Filter/FunctionScoreFilter.php

namespace AppBundle\Filter;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchDSL\Filter\TermFilter;
use ONGR\ElasticsearchDSL\Query\FunctionScoreQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\FilterManagerBundle\Filter\FilterInterface;
use ONGR\FilterManagerBundle\Filter\FilterState;
use ONGR\FilterManagerBundle\Filter\ViewData;
use ONGR\FilterManagerBundle\Filter\Widget\Choice\MultiTermChoice;
use ONGR\FilterManagerBundle\Search\SearchRequest;

/**
 * Filter for ONGR FilterManager. Helps to create function score boosting.
 */
class FunctionScoreFilter extends MultiTermChoice
{
    /**
     * @var FunctionScoreQuery
     */
    private $functionScore;

    /**
     * Public constructor
     */
    public function __construct()
    {
        $this->functionScore = new FunctionScoreQuery(new MatchAllQuery());
    }

    /**
     * Boost field by terms map. Map key saves term and value - weight.
     * Ie.: $map = ['XL' => 4, 'L' => 3.2, 'M' => 2.5, 'S' => 1.4]
     *
     * @param string $field
     * @param array  $map
     */
    public function boostTermByMap($field, $map)
    {
        foreach ($map as $term => $weight) {
            $filter = new TermFilter($field, $term);
            $this->functionScore->addWeightFunction($weight, $filter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modifySearch(Search $search, FilterState $state = null, SearchRequest $request = null)
    {
        $search->addQuery($this->functionScore);
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
  
## 2. Defining service  

Filter service must be tagged with `ongr_filter_manager.filter` tag, `filter_name` node is required.
  
```yaml
# app/config/services.yml

services:
    ongr_filter_manager.filter.custom_boost_filter:
      class: AppBundle\Filter\FunctionScoreFilter
      calls:
        - ['boostTermByMap', ['country', {'Ireland': 100}]]
      tags:
        - { name: ongr_filter_manager.filter, filter_name: custom_boost_filter }
```

This configuration means that we are going to boost all objects from Ireland by factor of 100. More about boosting in [official documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html).

## 3. Adding filter to manager

You can add custom filter in same way that you add regular filters. Say you want to add just created `foo_range` filter to `foo_manager`, your configuration would look like this:
```yaml
# app/config/config.yml

ongr_filter_manager:
    managers:
        search_list:
            filters:
                - custom_boost_filter
            repository: 'es.manager.default.product'
```

## 4. Using filter  

Filter can be used as other filters trough `FilterManager`, see one of our  [`examples`](../index.md#usage-examples).
