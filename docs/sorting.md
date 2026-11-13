# Sorting

By default, your search results will be sorted by their score according to Elasticsearch.
If you want to step in and influence the sorting you may do so using the default `orderBy()` function from Laravel Scout.

```php
use App\Models\Post;

$results = Post::search('Self-steering')
    ->orderBy('published_at', 'desc')
    ->get();
```

## Sorting by distance

Fields mapped as `geo_point` cannot be sorted with a plain `orderBy()`, Elasticsearch requires a `_geo_distance` sort for them.
Use the `GeoDistanceSort` class to sort by the distance between the field and an origin.
Scout's `orderBy()` only accepts a column and a direction, so add the sort to the builder's `orders`:

```php
use App\Models\Shop;
use JeroenG\Explorer\Domain\Syntax\GeoDistanceSort;

$builder = Shop::search('Bakery');
$builder->orders[] = new GeoDistanceSort('location', latitude: 52.37, longitude: 4.89, unit: 'km');

$results = $builder->get();
```

The arguments after the origin are optional: the sort order (`asc` by default, or `desc`), the distance `unit`, the `mode` (`min`, `max`, `median` or `avg`, useful when the field holds multiple points) and the `distanceType` (`arc` or `plane`).
