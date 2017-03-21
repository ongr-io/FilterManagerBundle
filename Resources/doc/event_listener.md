# Event Listener

## Creating an Event Listener

You can attach event listeners to any of the events dispatched during the `ONGRFilterManager` 
search. The name of each of the events is defined as a constant on the `ONGRFilterManagerEvents` class. 
Each event has their own event object:

Name | Constant | Argument passed to the listener
--- | --- | ---
`ongr_filter_manager.pre_search` | *PRE_SEARCH* | **PreSearchEvent**
`ongr_filter_manager.search_response` | *SEARCH_RESPONSE* | **SearchResponseEvent**
`ongr_filter_manager.pre_process_search` | *PRE_PROCESS_SEARCH* | **PreProcessSearchEvent**

## The Listener Class
For example, **SearchResponseEvent** listener might look like this:
```php
<?php

namespace AppBundle\EventListener;

use ONGR\FilterManagerBundle\Event\SearchResponseEvent;
// ...
class SearchResponseListener
{
    // ...
    public function onSearchResponse(SearchResponseEvent $event)
    {
        $results = $event->getDocumentIterator();
        // Do your magic
    }
    // ...
}
```

## Listener Configuration

To register an event listener you just have to tag it with the appropriate name. For example, **SearchResponseEventListener** configuration might look like this:

```yml
services:
    # ...
    app_bundle.search_response_listener:
        class: AppBundle\EventListener\SearchResponseEventListener
        tags:
            - { name: kernel.event_listener, event: ongr_filter_manager.search_response, method: onSearchResponse }
```
