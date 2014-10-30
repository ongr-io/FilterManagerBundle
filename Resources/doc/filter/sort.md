# Sort filter.

Filter used for sorting the documents.

For example, lets say we have `item` repository which contains the following data:

| item_id | item_color |
|---------|------------|
| 1       | red        |
| 2       | red        |
| 3       | blue       |
| 4       | green      |
| 5       | blue       |

If we have a sort filter with choice to sort on `item_color` in descending order and this choice is selected we will get:

| item_id | item_color |
|---------|------------|
| 1       | red        |
| 2       | red        |
| 4       | green      |
| 3       | blue       |
| 5       | blue       |

# Configuration

First, you have to specify the request field:

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `request_field`        | Request field used to pass the sort choice id (f.e. `www.page.com/?request_field=4`) |

After which you can specify multiple sort options/choices:

| Setting name           | Meaning                                                            |
|------------------------|--------------------------------------------------------------------|
| `label`                | Choice name to be used in templates. (f.e. `Title descending`)     |
| `field`                | Specifies the field in repository to sort on. (f.e. `item_color`)  |
| `order`                | Order to sort by. Default `asc`. Valid values: `asc`,  `desc`.     |
| `default`              | Specifies whether this choice is the default one. Default `false`. |

Example:
```yaml
#app/config/config.yml
ongr_filter_manager:
    managers:
        item_list:
            filters:
                - list_sorting
            repository: 'item'
    filters:
        sort:
            list_sorting:
                request_field: 'sort'
                choices:
                    - { label: Color ascending, field: item_color, default: true }
                    - { label: Color descending, field: item_color, order: desc }
```

# Twig view data

View data returned by this filter to be used in template:

| Method                  | Value                                            |
|-------------------------|--------------------------------------------------|
| getName()               | Filter name                                      |
| getResetUrlParameters() | Url parameters required to reset filter          |
| getState()              | Filter state                                     |
| getUrlParameters()      | Url parameters representing current filter state |
| getChoices()            | Returns a list of available sort choices         |

Each choice has its own data:

| Method             | Value                                      |
|--------------------|--------------------------------------------|
| isActive()         | Is this choice currently applied           |
| isDefault()        | Is this choice the default one             |
| getLabel()         | Choice label                               |
| getUrlParameters() | Returns a list of available choices        |
                    
