# Sorting

By default, your search results will be sorted by their score according to Elasticsearch.
If you want to step in and influence the sorting you may do so using a simplified implementation in Explorer.
Currently it is only possible to define one sort order.

```php
use App\Models\Post;
use \JeroenG\Explorer\Domain\Syntax\Sort;

$results = Post::search('Self-steering')
    ->sort(new Sort('published_at'))
    ->get();
```

The first parameter of a `Sort()` object is the name of the field, an optional second parameter is for the order.

```php
use \JeroenG\Explorer\Domain\Syntax\Sort;

new Sort('id', Sort::ASCENDING); // the default
new Sort('id', Sort::DESCENDING);
```
