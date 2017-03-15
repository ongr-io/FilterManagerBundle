# Relations

Filter relations enable you to control the filters that will affect the 
additional information returned by the filter in question. 

## Use Case

To illustrate this, imagine that you have a choice filter that operates on the
colors of your products. In addition to simply filtering out the data, the filter
provides the list of choices with available colors, the count and the url 
parameters for every choice. This list is affected by other filters that are in
use at any given moment. Therefore, if you also have a price filter set up, 
the available choices of your color filter will decrease when you limit the 
price range of the product list.

Relations describe the relationships between filters and allows you to alter
this behavior. For example, you can set up the choice filter to ignore the 
price filter. If you do that, different ranges of the price filter will not 
affect the list of choices provided by the color filter, which in this case will
always stay the same.

## Configuring Relations

Relations can be specified in the `relations` node of each of your filters in their
configuration. There are two types of relations: `search` and `reset`. The 
`search` type controls what filters will affect the additional information that
is provided by the filter, much like in the use case example. The `reset` type
on the other hand, controls what active parameters from the other filters go to
the `urlParameters` and the `resetUrlParameters` of the `ViewData` instance
returned by the filter. For more information visit the [View Data chapter](http://docs.ongr.io/FilterManagerBundle/ViewData).

In addition, every type of relations must be set to either `include` or `exclude`
the list of filters. Here is a full table of options:

| Type     | Subtype   | Description                                                                                   |
|:--------:|:---------:|:---------------------------------------------------------------------------------------------:|
| `search` | `include` | Includes only the selected filters to the choice list formation                               | 
| `search` | `exclude` | Includes all filters except selected ones to the choice list formation                        | 
| `reset`  | `include` | Includes the active parameters of only the selected filters to the url parameter formation    | 
| `reset`  | `exclude` | Includes the active parameters of all filters except selected ones to url parameter formation | 

Example configuration of the color filter described earlier:
 
```yml

ongr_filter_manager:
    ...
    filters:
        color:
            type: multi_choice
            request_field: c
            document_field: color
            relations:
                search:
                    exclude:
                        - price
    ...

```

