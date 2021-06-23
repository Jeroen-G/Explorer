# Aggregations
Aggregations are part of your search query and can summarise your data.
You can read more about aggregations in Elasticsearch in the [official documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations.html).
At this moment not all aggregation types are build in, but creating the missing ones should be doable (and these additions to the package are very welcome).

Adding aggregations makes your search query more advanced.
Here is an example from the demo application:

```php
$search = Cartographer::search();
$search->aggregation('places', new TermsAggregation('place'));

$results = $search->raw();
$aggregations = $results->aggregations();
```

This will return an array of metrics on how many times every place is present in the Elasticsearch index.  
