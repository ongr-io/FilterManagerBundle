# View Data

As you can see from the [basics section]('http://docs.ongr.io/FilterManagerBundle/Basics'),
every filter generates a `ViewData` object after the request is handled. There are several
types of `ViewData` objects and they vary from filter to filter. This section is dedicated 
to explaining the general concepts behind it.

### What is View Data

ONGR `FilterManager` provides functionality for easy list formation. `ViewData` objects
include helpers for each filter that can be accessed in Twig view. This information 
helps to keep track of filter states and provides additional information for some specific
filters. Usually this additional information is retrieved from aggregations and used to
form choices, range bounds or other useful features.

### Default View Data

By default, the view data returned by the filters is an instance of `ONGR\FilterManagerBundle\Filter\ViewData`.
This object holds the information about:

| Property           | Description
|--------------------|------------------
| Name               | The name of the filter
| State              | The state of the filter (includes the current value and whether it's active)
| UrlParameters      | Url parameters representing current filter state
| ResetUrlParameters | Current url parameters of request excluding the ones from the filter
| Tags               | Array of filter specific tags

This is the information that can be retrieved from every filter. However, there are 
some more complex filters and the view data objects they return extend upon the default view 
data class.
 
> For more information on specific filters view data check the dedicated docs of these filters  

### Accessing View Data

When you call the filter managers `handleResponse` method, it returns `ONGR\FilterManagerBundle\Search\SearchResponse`
instance as result. You can get to the array with the view data objects from each filter
by calling `getFilters` method on this result and pass this array to the template. 

For more information on how to work with filters please refer to the [basics chapter](http://docs.ongr.io/FilterManagerBundle/Basics) 
