# Preparing data
[Text analysis](text-analysis.md) is perfect when you want to alter how Elasticsearch deals with a query, or with the data when it is being indexed.
However, sometimes you do not necessarily want to meddle with analyzer, but rather just make a quick change to the data _before_ it is indexed, perhaps conditionally.

For this, Explorer gives you the ability to 'prepare' your searchable data.
To use this you will need to implement the `JeroenG\Explorer\Application\BePrepared` interface on your model and add the `prepare($data)` function.
The data that is passed to the prepare function is what Laravel Scout [generates](https://laravel.com/docs/scout#configuring-searchable-data) in the `toSearchableArray()` method.

The prepared data might be very simple:

```php
public function prepare(array $searchable): array
{
    $searchable['name'] = ucfirst($searchable['name']);

    return $searchable;
}
```

Or you could use something like Laravel's pipelines to do much more complex stuff:

```php
public function prepare(array $searchable): array
{
    $searchable['content'] = (new Illuminate\Pipeline\Pipeline())
                ->send($searchable['content'])
                ->through([
                    App\Formatters\ConvertMarkdown::class,              
                    App\Formatters\StripTags::class,
                    App\Formatters\EncodeEmoji::class,
                ])
                ->thenReturn();

    return $searchable;
}
```
