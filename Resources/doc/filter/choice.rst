=============
Choice filter
=============

This filter groups values of repository in a specified field, and returns available options.
If you select one of the options, *choice filter* will return item list filtered by it.

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

If we apply *choice filter* on field `item_color`, it will return

+-------------+
| choices     |
+=============+
| red         |
+-------------+
| green       |
+-------------+
| blue        |
+-------------+

We can then select a value from this list and get all items for it, lets say we select the choice `blue`.

+---------+------------+
| item_id | item_color |
+=========+============+
| 3       | blue       |
+---------+------------+
| 5       | blue       |
+---------+------------+

~~~~~~~~~~~~~
Configuration
~~~~~~~~~~~~~

+------------------------+--------------------------------------------------------------------------------------+
| Setting name           | Meaning                                                                              |
+========================+======================================================================================+
| `request_field`        | Request field used to view the selected page. (e.g. `www.page.com/?request_field=4`) |
+------------------------+--------------------------------------------------------------------------------------+
| `field`                | Specifies the field in repository to apply this filter on. (e.g. `item_color`)       |
+------------------------+--------------------------------------------------------------------------------------+
| `sort`                 | Sorts the choices based on your configuration.                                       |
+------------------------+--------------------------------------------------------------------------------------+

~~~~~~~~~~~~~~~~~~~~~
Sorting configuration
~~~~~~~~~~~~~~~~~~~~~

+------------------------+------------------------------------------------------------------------------------------+
| Setting name           | Meaning                                                                                  |
+========================+==========================================================================================+
| `type`                 | You can sort either by the `_term` which was aggregated or by the `_count` of the terms. |
+------------------------+------------------------------------------------------------------------------------------+
| `order`                | Specifies the ordering direction. Either ascending or descending.                        |
+------------------------+------------------------------------------------------------------------------------------+
| `priority`             | Highest priority term names, the first terms to be shown in choices list.                |
+------------------------+------------------------------------------------------------------------------------------+

Example:

.. code-block:: yaml

    #app/config/config.yml
    ongr_filter_manager:
        managers:
            item_list:
                filters:
                    - colors
                repository: 'item'
        filters:
            choice:
                colors:
                    request_field: 'colors'
                    field: item_colors
                    sort:
                      type: _term
                      order: asc
                      priorities:
                         - red

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
| getChoices()            | Returns a list of available choices              |
+-------------------------+--------------------------------------------------+

Each choice has its own data:

+--------------------+--------------------------------------------+
| Method             | Value                                      |
+====================+============================================+
| isActive()         | Is this choice currently applied           |
+--------------------+--------------------------------------------+
| isDefault()        | Is this choice the default one             |
+--------------------+--------------------------------------------+
| getCount()         | Return the number of items for this choice |
+--------------------+--------------------------------------------+
| getLabel()         | Choice label                               |
+--------------------+--------------------------------------------+
| getUrlParameters() | Returns a list of available choices        |
+--------------------+--------------------------------------------+

* `Multi choice filter <multi_choice.html>`_
* `Document field filter <document_field.html>`_
* `Match filter <match.html>`_
* `Pager filter <pager.html>`_
* `Sort filter <sort.html>`_
