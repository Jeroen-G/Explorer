#Debugging
Sometimes you might wonder why certain results are or aren't returned.

Here is an example from the Explorer demo app, although not with a complex query:

```php
class SearchController
{
    public function __invoke(SearchFormRequest $request)
    {
        $people = Cartographer::search($request->get('keywords'))->get();

        return view('search', [
            'people' => $people,
        ]);
    }
}
```

To debug this search query you can call the static `debug` method on the Elastic Engine for Laravel Scout:

```php
use JeroenG\Explorer\Infrastructure\Scout\ElasticEngine;

$debug = ElasticEngine::debug();
```

The debug class that this method returns can give you the last executed query as an array or as json.
You should be able to copy-paste the json as a direct query to Elasticsearch.

```php
$lastQueryAsArray = ElasticEngine::debug()->array();
$lastQueryAsJson = ElasticEngine::debug()->json();
```
