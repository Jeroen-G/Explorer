# Pagination and search result size

You can use Laravel Scout's [pagination](https://laravel.com/docs/scout#pagination) feature to get paginated results from Explorer.

By default, Elasticsearch returns only the first 10 search results as you can see in their [documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/paginate-search-results.html).
Laravel Scout has a `take()` method and Explorer uses that to set a custom search result size for Elasticsearch.
For example to get a maximum of 300 search results:

```php
use App\Models\Post;

$results = Post::search('Spartans')->take(300)->get();
```
