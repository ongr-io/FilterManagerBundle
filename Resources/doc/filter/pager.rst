============
Pager filter
============

Filter which provides pagination functionality by returning documents for the selected page.

For example, lets say we have `item` repository which contains the following data:

+---------+------------+
| item_id | item_color |
+=========+============+
| 1       | red        |
+---------+------------+
| 2       | red        |
+---------+------------+
| 3       | blue       |
+---------+------------+
| 4       | green      |
+---------+------------+
| 5       | blue       |
+---------+------------+

If we have a pager filter with `count_per_page` set to `2` and we request for the second page we will get:

+---------+------------+
| item_id | item_color |
+=========+============+
| 3       | blue       |
+---------+------------+
| 4       | green      |
+---------+------------+

**To use this filter, you'll need to add pager bundle to your composer requirements**

.. code-block:: bash

    $ php composer.phar require ongr/pager-bundle "~1.0"

..

~~~~~~~~~~~~~
Configuration
~~~~~~~~~~~~~

+------------------------+--------------------------------------------------------------------------------------+
| Setting name           | Meaning                                                                              |
+========================+======================================================================================+
| `request_field`        | Request field used to pass the page number (f.e. `www.page.com/?request_field=4`)    |
+------------------------+--------------------------------------------------------------------------------------+
| `count_per_page`       | Number of items per page. (default `10`).                                            |
+------------------------+--------------------------------------------------------------------------------------+
| `max_pages`            | Maximum number of pages displayed in pager at once (default `8`).                    |
+------------------------+--------------------------------------------------------------------------------------+

Example:

.. code-block:: yaml

    #app/config/config.yml
    ongr_filter_manager:
        managers:
            item_list:
                filters:
                    - list_pager
                repository: 'item'
        filters:
            pager:
                list_pager:
                    request_field: 'page'
                    count_per_page: 9
                    max_pages: 6

..

~~~~~~~~~~~~~~
Twig view data
~~~~~~~~~~~~~~

View data returned by this filter to be used in template:

+-------------------------+--------------------------------------------------+
| Method                  | Value                                            |
+=========================+==================================================+
| getName()               | Filter name                                      |
+-------------------------+--------------------------------------------------+
| getResetUrlParameters() | Url parameters required to reset filter          |
+-------------------------+--------------------------------------------------+
| getState()              | Filter state                                     |
+-------------------------+--------------------------------------------------+
| getUrlParameters()      | Url parameters representing current filter state |
+-------------------------+--------------------------------------------------+
| getPagerService()       | Returns pager service to be used in template     |
+-------------------------+--------------------------------------------------+

* `Choice filter <choice.rst>`_
* `Multi choice filter <multi_choice.rst>`_
* `Document field filter <document_field.rst>`_
* `Match filter <match.rst>`_
* `Sort filter <sort.rst>`_
