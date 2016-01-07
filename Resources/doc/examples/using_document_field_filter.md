# Using `document_field` filter
 
This example will provide real use case of `document_field` filter. Two different document types will be created: `country` and `city`. Every city will contain id of country. We want to display page for country and list cities in that country.
 
## Configuration
 
 First of Elasticsearch, FilterManager and Router bundles have to be configured.
 
```yaml
 # app/config/config.yml
 
ongr_elasticsearch:
    analysis:
        analyzer:
            urlAnalyzer:
                type: custom
                tokenizer: keyword
                filter: [lowercase]
    connections:
        default:
            hosts:
                - 127.0.0.1:9200
            index_name: world
            analysis:
                analyzer:
                    - urlAnalyzer
            settings:
                number_of_shards: 1
                number_of_replicas: 0
                index:
                    refresh_interval: -1
    managers:
        default:
            connection: default
            profiler: true
            mappings:
                - AppBundle
                
ongr_filter_manager:
    managers:
        cities_list:
            filters:
                - country_filter
            repository: 'es.manager.default.city'
    filters:
        document_field:
            country_filter:
                request_field: document
                field: country_id

ongr_router:
    es_manager: default
    seo_routes:
        Country:
            _route: country_page
            _controller: AppBundle:Default:countryPage
            _id_param: _id
            _default_route: homepage
```
 
## Define documents
         
Next step is to define documents:

```php
// src/AppBundle/Document/Country.php
    
namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\RouterBundle\Document\SeoAwareTrait;

/**
 * @ES\Document(type="country")
 */
class Country
{
    use SeoAwareTrait;

    /**
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $name;
}
```

```php
// src/AppBundle/Document/City.php

namespace AppBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;

/**
 * @ES\Document(type="city")
 */
class City
{
    /**
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $name;

    /**
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $countryId;
}
```

## Importing sample data

This step is here only for demonstration purposes.

Create file `countries.json` with following content:

```json
[
{"count":8,"date":"2015-12-16T11:47:22+0200"},
{"_type":"country","_id":"2","_source":{"name":"USA","urls":[{"url":"country\/USA\/"}],"expired_urls":[]}},
{"_type":"city","_id":"a","_source":{"name":"Miami","country_id":2}},
{"_type":"city","_id":"b","_source":{"name":"New York","country_id":2}},
{"_type":"city","_id":"c","_source":{"name":"San Francisco","country_id":2}},
{"_type":"country","_id":"1","_source":{"name":"Germany","urls":[{"url":"country\/Germany\/"}],"expired_urls":[]}},
{"_type":"city","_id":"d","_source":{"name":"Berlin","country_id":1}},
{"_type":"city","_id":"e","_source":{"name":"Munich","country_id":1}},
{"_type":"city","_id":"f","_source":{"name":"Hamburg","country_id":1}}
]
```

To import this data run `app/console ongr:es:index:import countries.json`.

## Using filter

Last step is to use created filter. Custom action will be created:

```php
// src/AppBundle/Controller/DefaultController.php

namespace AppBundle\Controller;

use AppBundle\Document\Country;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/countryPage/{country}", name="country_page")
     */
    public function countryPageAction(Country $document, Request $request)
    {
        $manager = $this->get('ongr_filter_manager.cities_list');

        $cities = [];

        foreach ($manager->handleRequest($request)->getResult() as $city) {
            $cities[] = $city->name;
        }

        return new Response('Cities in ' . $document->name . ': ' . implode(', ', $cities));
    }
}
```

Now if you go to `example.com/country/Germany/` you should see:
```bash
Cities in Germany: Berlin, Munich, Hamburg
```

Response at `example.com/country/USA/` should be:
```bash
Cities in USA: Miami, New York, San Francisco
```
