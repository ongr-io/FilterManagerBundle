Configuration
=============

FilterManager is used for retrieving filtered entities from database. It provides ties between commonly used filtering options and UI elements with Elasticsearch repositories. You can use it from a single controller.

It requires *managers* and *filters* to be configured to work. *Managers* are used to specify repository which will be used to fetch data and *filters* that will be used to filter fetched data.

A more complex example:
-----------------------

.. code-block:: yaml

    ongr_filter_manager:
        managers:
            item_list:
                filters:
                    - sorting
                    - country
                    - search
                    - category
                repository: 'item'
            category_list:
                filters:
                    - category_pager
                    - sorting
                repository: 'category'
        filters:
            sort:
                sorting:
                    request_field: 'sort'
                    choices:
                        - { label: Title ascending, field: title, default: true }
                        - { label: Title descending, field: title, order: desc }
            choice:
                country:
                    request_field: 'country'
                    field: origin.country
            match:
                search:
                    request_field: 'q'
                    field: title
            document_field:
                category:
                    request_field: 'document'
                    field: category_id
            pager:
                item_pager:
                    request_field: 'page'
                    count_per_page: 9
                category_pager:
                    request_field: 'category_list_page'
                    count_per_page: 3

..

This configuration would create six filters, which would be used by a single or both managers. The resulting structure can be seen as a table:

+----------------+---------------+-----------------------------+
| Filter name    | Filter type   | Manager names               |
+================+===============+=============================+
| sorting        | sort          | item_list AND category_list |
+----------------+---------------+-----------------------------+
| country        | choice        | item_list                   |
+----------------+---------------+-----------------------------+
| search         | match         | item_list                   |
+----------------+---------------+-----------------------------+
| category       | document_field| item_list                   |
+----------------+---------------+-----------------------------+
| item_pager     | pager         | item_list                   |
+----------------+---------------+-----------------------------+
| category_pager | pager         | category_list               |
+----------------+---------------+-----------------------------+

Configuration for different filters
-----------------------------------

A detailed explanation and configuration for each type of filter can be found on `main page <index.html#filters>`__.
