####################
Using filter manager
####################

================
Using controller
================

~~~~~~~~~~~~~~~~~~~~~~~~
Using default controller
~~~~~~~~~~~~~~~~~~~~~~~~

Once you set up your `*managers* <manager.rst>`_ you don't need to create a controller for each one,
you can just use default manager controller `ONGRFilterManagerBundle:Manager:manager`.

Example:

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

This specific example will render template `AcmeDemoBundle:List:results.html.twig`,
with :ref:`SearchResponse` from :ref:`FiltersManager` named `item_list`.

------------------------
Using custom controller.
------------------------

You can still use custom controller by getting your needed manager from the container.
This way you can add your custom variables if needed.

Example:

.. code-block:: yaml

    #src/Acme/DemoBundle/Resources/config/routing.yml
    ongr_search_page:
        pattern: /list
        methods:  [GET]
        defaults:
            _controller: AcmeDemoBundle:List:index

..

.. code-block:: php

    //src/Acme/DemoBundle/Controller/ListController.php
    /**
     * Controller for list pages.
     */
    class ListController extends Controller
    {
        /**
         * Renders my list page
         *
         * @param Request $request
         *
         * @return Response
         */
        public function indexAction(Request $request)
        {
            return $this->render(
                'AcmeDemoBundle:List:results.html.twig',
                [
                    'filter_manager' => $this->getProductsData($request),
                    'my_custom_variable' => $var,
                ]
            );
        }

        /**
         * Returns item list
         *
         * @param Request $request
         *
         * @return array
         */
        private function getProductsData($request)
        {
            //here we get our filter manager
            return $this->get('ongr_filter_manager.item_list')->execute($request);
        }
    }

..

------------------
Template variables
------------------

If you're using default controller, :ref:`SearchResponse` from :ref:`FiltersManager` will be named `filter_manager` in template,
otherwise it's whatever you call it in your controller.

You can use :ref:`SearchResponse` to get results in your template.
Example:

.. code-block:: twig

    {% for item in filter_manager.result %}
        <b>{{ item.title }}</b>
    {% endfor %}

..

You can also use it to get data about your filter.
Example:

.. code-block:: twig

    Pager url parameters: {{ filter_manager.filters.pager.getUrlParameters() }}

..

A complete list of parameters for each filter can be found in its documentation:

* `Choice filter <filter/choice.rst>`_
* `Multi choice filter <filter/multi_choice.rst>`_
* `Document field filter <filter/document_field.rst>`_
* `Match filter <filter/match.rst>`_
* `Pager filter <filter/pager.rst>`_
* `Sort filter <filter/sort.rst>`_



.. _[SearchResponse]:`SearchResponse <https://github.com/ongr-io/FilterManagerBundle/blob/master/Search/SearchResponse.php>`_

.. _[FiltersManager]:`FiltersManager <https://github.com/ongr-io/FilterManagerBundle/blob/master/Search/FiltersManager.php>`_
