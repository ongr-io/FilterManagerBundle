# ONGR FilterManagerBundle

Filter manager is used for listing documents. It provides ties between commonly used filtering options and UI elements with Elasticsearch repositories.
You can use it from a single controller.
It is important to mention that filtering is everything what has impact on list, it can be:
- Filtering on specific field value object have (color, country etc.)
- Filtering range (price range, distance from point etc.)
- Documents list pagination. Paging changes representation of list, so it is considered to be filter and is treated like one.
- Documents list sorting. Same as paging - sorting is filter in this bundle.
- Any custom factor which has influence (not always directly visible) on result list. It can exclude, boost, modify some results, collect some metrics or any other action you can imagine.

[![](https://travis-ci.org/ongr-io/FilterManagerBundle.svg?branch=master)](https://travis-ci.org/ongr-io/FilterManagerBundle)
[![](https://scrutinizer-ci.com/g/ongr-io/FilterManagerBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ongr-io/FilterManagerBundle/?branch=master)
[![](https://scrutinizer-ci.com/g/ongr-io/FilterManagerBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ongr-io/FilterManagerBundle/?branch=master)
[![](https://insight.sensiolabs.com/projects/44c0f05e-a9a8-41ab-9acf-1225cef2887c/mini.png)](https://insight.sensiolabs.com/projects/44c0f05e-a9a8-41ab-9acf-1225cef2887c)
[![](https://poser.pugx.org/ongr/filter-manager-bundle/downloads)](https://packagist.org/packages/ongr/filter-manager-bundle)
[![](https://poser.pugx.org/ongr/filter-manager-bundle/v/stable)](https://packagist.org/packages/ongr/filter-manager-bundle)
[![](https://poser.pugx.org/ongr/filter-manager-bundle/v/unstable)](https://packagist.org/packages/ongr/filter-manager-bundle)
[![](https://poser.pugx.org/ongr/filter-manager-bundle/license)](https://packagist.org/packages/ongr/filter-manager-bundle)

## Instalation

### Step 1: Install FilterManager bundle

FilterManager bundle is installed using [Composer](https://getcomposer.org).

```bash
$ composer require ongr/filter-manager-bundle "~1.0"
```

> Please note that filter manager requires Elasticsearch bundle, guide on how to install and configure it can be found [here](https://github.com/ongr-io/ElasticsearchBundle).

### Step 2: Enable FilterManager bundle

Enable Filter Manager bundle in your AppKernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
        new ONGR\FilterManagerBundle\ONGRFilterManagerBundle(),
        // ...
    );
    
    // ...
}
```

### Step 3: Add configuration for manager

Add minimal configuration for Elasticsearch and FilterManager bundles.

```yaml
# app/config/config.yml

ongr_elasticsearch:
    connections:
        default:
            hosts:
                - 127.0.0.1:9200
            index_name: items
    managers:
        default:
            connection: default

ongr_filter_manager:
    managers:
        search_list: # <- Filter manager name
            filters:
                - country
            repository: 'es.manager.default.product'
    filters:
        choice:
            country: # <- Filter name
                request_field: 'country'
                field: country
```
> Note that `Product` document has to be defined. More about that in ElasticsearchBundle [documentation](https://github.com/ongr-io/ElasticsearchBundle/blob/master/Resources/doc/mapping.md).

In this particular example, we defined a single filter manager named `search_list` to filter documents from product repository, and we will be using the filter named `country` to filter on countries defined in document.

### Step 4: Use your new bundle

FilterManagerBundle is ready to use. You can take a look at our [search page example](Resources/doc/examples/search_example.md).


## Documentation

The online documentation of the bundle is in [Github](Resources/doc/index.md).

## License

This bundle is covered by the MIT license. Please see the complete license in the bundle [LICENSE](LICENSE) file.
