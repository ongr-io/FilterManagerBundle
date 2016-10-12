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
                - search
                - pager
                - price
                - sorting
                - color
                - brand
                - people
                - material
                - app_category
            # the repository that this manager will filter on
            repository: 'es.manager.default.product'
        category_list:
            filters:
                - search
            repository: 'es.manager.default.category'

    filters:
        # a node for a filter type can contain multiple filters
        multi_choice:
            # configuration of individual filter named `color`
            color:
                request_field: 'c'
                field: color
                size: 10
                relations:
                    search:
                        include:
                            - people

            material:
                request_field: 'm'
                field: material
                size: 10
        choice:
            brand:
                request_field: 'b'
                field: brand
                size: 10
            people:
                request_field: 'p'
                field: variants.people
                size: 10
                relations:
                    search:
                        include:
                            - color
        match:
            search:
                request_field: 'q'
                field: title
        pager:
            pager:
                request_field: 'page'
                count_per_page: 12
                max_pages: 10
        sort:
            sorting:
                request_field: 'sort'
                choices:
                    - { label: filter.price_asc, field: price, default: true }
                    - { label: filter.price_desc, field: price, order: desc }
                    - { label: filter.title_asc, field: title }
                    - { label: filter.title_desc, field: title, order: desc }
        range:
            price:
                request_field: 'price'
                field: price
                inclusive: true

```