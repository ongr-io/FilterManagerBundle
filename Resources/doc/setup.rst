===================
FilterManager setup
===================

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Step 1: Install FilterManager bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

FilterManager bundle is installed using `Composer <https://getcomposer.org>`_.

.. code-block:: bash

    $ php composer.phar require ongr/filter-manager-bundle "~1.0"

..

    --------------------
    Elasticsearch bundle
    --------------------
    Please note that filter manager requires Elasticsearch bundle, guide on how to install it
     can be found `here <https://github.com/ongr-io/ElasticsearchBundle/tree/master/Resources/doc/setup.rst>`_.


~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Step 2: Enable FilterManager bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Enable Filter Manager bundle in your AppKernel:

.. code-block:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            // Elasticsearch bundle - should be already added.
            new ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
            // The line you need to add.
            new ONGR\FilterManagerBundle\ONGRFilterManagerBundle(),
        );
    }

..

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Step 3: Add configuration for manager
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add minimal configuration for FilterManager bundle.

.. code-block:: yaml

    #app/config/config.yml
    ongr_filter_manager:
        managers:
            item_list:
                filters:
                    - sorting
                repository: 'item'
        filters:
            sort:
                sorting:
                    request_field: 'sort'
                    choices:
                        - { label: Title ascending, field: title, default: true }
                        - { label: Title descending, field: title, order: desc }

..

    This is the basic example only, for more information about manager configuration, please take a look at
 `manager <manager.rst>`_ chapter.

In this particular example, we defined a single manager named `item_list` to filter documents from `item` repository,
and we'll be using the filter named `sorting` to sort the item list with title either descending or ascending.

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Step 4: Add configuration for routing
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add a simple route:

.. code-block:: yaml

    #src/Acme/DemoBundle/Resources/config/routing.yml
    ongr_search_page:
        pattern: /list
        methods:  [GET]
        defaults:
            _controller: ONGRFilterManagerBundle:Manager:manager
            managerName: "item_list"
            template: "AcmeDemoBundle:List:results.html.twig"

..

This example will handle `www.mypage.com/list` route, rendering template `AcmeDemoBundle:List:results.html.twig`
with the filtered manager data.

    You can also use your own custom controller specifying a route if needed (example can be found at [usage](usage.md) chapter).

~~~~~~~~~~~~~~~~~~~~~~~~~~~
Step 5: Use your new bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Usage documentation for the FilterManager bundle is available `here <usage.rst>`_.
