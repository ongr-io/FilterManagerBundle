# Configuration

Filter Manager is one of the most powerful parts of ONGR and can largely be controlled via its configuration.
Therefore understanding of configuration is essential for the usage of this component.

Here is an example of Filter Manager Bundle configuration:

```yaml

ongr_filter_manager:
    # list of configured filter managers, any number of managers can be defined
    managers:
        product_list:
            # a list of filters by name that this manager holds
            filters:
                - material
                - people
                - pagination
                - price
                - sorting
            # the repository that this manager will filter on
            repository: 'es.manager.default.product'
        category_list:
            filters:
                - people
                - pagination
            repository: 'es.manager.default.category'

    filters:
        # a node for a filter type can contain multiple filters
        # configuration of individual filter named `color`
        color:
            type: multi_choice
            request_field: c
            document_field: color
            options:
                size: 10
                relations:
                    search:
                        include:
                            - people
        material:
            type: multi_choice
            request_field: m
            field: material
        brand:
            type: choice
            request_field: 'b'
            document_field: brand
            options:
                size: 10
        people:
            type: choice
            request_field: 'p'
            document_field: variants.people
            options:
                size: 10
                relations:
                    search:
                        include:
                            - color
        # Filter name cannot be the same as filter type.
        pagination:
            type: pager
            request_field: page
            document_field: ~
            options:
                count_per_page: 12
                max_pages: 10
        sorting:
            type: sort
            request_field: 'sort'
            options:
                choices:
                    - { label: filter.price_asc, field: price, default: true }
                    - { label: filter.price_desc, field: price, order: desc }
                    - { label: filter.title_asc, field: title }
                    - { label: filter.title_desc, field: title, order: desc }
```