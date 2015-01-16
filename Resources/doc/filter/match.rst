============
Match filter
============

This filter searches for a matching value in the specified field.

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

If we apply *match filter* on item_colors field with a value of `red`, we will get.

+---------+------------+
| item_id | item_color |
+=========+============+
| 1       | red        |
+---------+------------+
| 2       | red        |
+---------+------------+

~~~~~~~~~~~~~
Configuration
~~~~~~~~~~~~~

+------------------------+--------------------------------------------------------------------------------------+
| Setting name           | Meaning                                                                              |
+========================+======================================================================================+
| `request_field`        | Request field used to specify filter value. (f.e. `www.page.com/?request_field=4`)   |
+------------------------+--------------------------------------------------------------------------------------+
| `field`                | Specifies the field in repository to apply this filter on. (f.e. `item_color`)       |
+------------------------+--------------------------------------------------------------------------------------+

Example:

.. code-block:: yaml

    #app/config/config.yml
    ongr_filter_manager:
        managers:
            item_list:
                filters:
                    - search
                repository: 'item'
        filters:
            match:
                search:
                    request_field: 'color'
                    field: item_colors

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

* `Choice filter <filter/choice.rst>`_
* `Multi choice filter <filter/multi_choice.rst>`_
* `Document field filter <filter/document_field.rst>`_
* `Pager filter <filter/pager.rst>`_
* `Sort filter <filter/sort.rst>`_