# Basic Usage

Since version `v2.0.0` all filters was unified and from now on all of them shares the same required configuration fields.

## Configuration 

| Setting name           | Meaning                                                                              |
|------------------------|--------------------------------------------------------------------------------------|
| `type`*                | In order to use `choice` filter you need to add this config.                         |
| `request_field`*       | Request field used to view the selected page. (e.g. `www.page.com/?request_field=4`) |
| `document_field`*      | Specifies the field in repository to apply this filter on. (e.g. `item_color`)       |
| `tags`                 | Array of filter specific tags that will be accessible at Twig view data.             |
| `relations`            | Read more about `relations` at dedicated topic [here](http://docs.ongr.io/FilterManagerBundle/Relations)           |
| `options`              | Array of filter specific options. Every filter might have different options, check in specific filter docs        |

> `*` are required for every filter.