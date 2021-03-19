# Sorting

By default, your search results will be sorted by their score according to Elasticsearch.
If you want to step in and influence the sorting you may do so using the default `orderBy()` function from Laravel Scout.

```php
use App\Models\Post;

$results = Post::search('Self-steering')
    ->orderBy('published_at', 'desc')
    ->get();
```
