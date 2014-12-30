> How to setup the bundle documentation is [here](setup.md).

# Manager configuration
FilterManager bundle requires *managers* and *filters* configured to work.

*Managers* are used to specify repository which will be filtered and which *filters* will be available for the given repository.

*Filters* are a set of rules, by which the documents will be filtered.

# A more complex example:

```yaml
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
```

This configuration would create six filters, which would be used by a single or both managers, the resulting structure can be seen as a table:

| Filter name    | Filter type   | Manager names               |
|----------------|---------------|-----------------------------|
| sorting        | sort          | item_list AND category_list |
| country        | choice        | item_list                   |
| search         | match         | item_list                   |
| category       | document_field| item_list                   |
| item_pager     | pager         | item_list                   |
| category_pager | pager         | category_list               |

# Configuration for different filters
A detailed explanation and configuration for each type of filter can be found on its documentation page:

* [Choice filter](filter/choice.md)
* [Multi choice filter](filter/multi_choice.md)
* [Document field filter](filter/document_field.md)
* [Match filter](filter/match.md)
* [Pager filter](filter/pager.md)
* [Sort filter](filter/sort.md)
