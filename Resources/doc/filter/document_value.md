# Using `document_value` filter
 
Document value filter is tightly connected to router bundle. It uses a document object provided by route match to grab a specific
 value from it and form a term query with that value. Usually it might be used for category contents filtering.
 
The example below will provide real use case of `document_value` filter. Two different document types will be created: `country` and `city`.
 Every city will contain a key of country. We want to display page for country and list cities in that country.
 
## Configuration
 
 First of Elasticsearch, FilterManager and Router bundles have to be configured.
 
```yaml
 # app/config/config.yml
 
ongr_elasticsearch:
    analysis:
        analyzer:
            urlAnalyzer: #for router bundle
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
    managers:
        default:
            connection: default
            mappings:
                - AppBundle
                
ongr_filter_manager:
    managers:
        cities_list:
            filters:
                - country_filter
            repository: 'es.manager.default.city'
    filters:
        document_value:
            country_filter:
                request_field: document # Use only `document` value, unless you have your own router.
                field: country
                document_field: key

ongr_router:
    es_manager: default
    seo_routes:
        country: AppBundle:Default:countryPage
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
    public $key;

    /**
     * @ES\Property(type="string")
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
     * @ES\Property(type="string")
     */
    public $name;

    /**
     * In this field will be stored related country object key.
     *
     * @ES\Property(type="string", options={"index"="not_analyzed"})
     */
    public $country;
}
```

## Importing sample data

This step is here only for demonstration purposes.

Create file `countries.json` with following content:

```json
[
{"count":5,"date":"2015-12-16T11:47:22+0200"},
{"_type":"country","_id":"1","_source":{"name":"USA", "key":"usa", "url":"/usa"},
{"_type":"city","_id":"a","_source":{"name":"Miami","country":"usa"}},
{"_type":"city","_id":"b","_source":{"name":"New York","country":"usa"}},
{"_type":"country","_id":"2","_source":{"name":"UK", "key":"uk", "url":"/uk"},
{"_type":"city","_id":"c","_source":{"name":"London","country":}}
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
    public function countryPageAction(Request $request, Country $document)
    {
        $manager = $this->get('ongr_filter_manager.cities_list')->handleRequest($request);

        $cities = [];

        foreach ($manager->getResult() as $city) {
            $cities[] = $city->name;
        }

        return new Response('Cities in ' . $document->name . ': ' . implode(', ', $cities));
    }
}
```

Now if you go to `example.com/usa/` you should see:

```bash
Cities in USA: Miami, New York
```

Response at `example.com/uk/` should be:

```bash
Cities in UK: London
```
