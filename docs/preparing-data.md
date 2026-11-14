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

## Ingest pipelines
Elasticsearch itself can also process documents before indexing them, using an [ingest pipeline](https://www.elastic.co/guide/en/elasticsearch/reference/current/ingest.html).
Pipelines can, for example, extract the text from attached files, enrich documents with data from other indices or parse and convert fields.

To send your documents through a pipeline, implement the `JeroenG\Explorer\Application\IngestPipeline` interface on your model and return the name of the pipeline from the `ingestPipeline()` function:

```php
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IngestPipeline;
use Laravel\Scout\Searchable;

class Post extends Model implements Explored, IngestPipeline
{
    use Searchable;

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title' => 'text',
            'attachment' => [
                'type' => 'object',
                'properties' => [
                    'content' => 'text',
                ],
            ],
        ];
    }

    public function ingestPipeline(): string
    {
        return 'attachment';
    }
}
```

Explorer will then index every document of the model through that pipeline, both when importing models and when keeping them in sync.
Note that Explorer does not create the pipeline for you, so it has to exist in Elasticsearch before any document is indexed.
You can create it for example with Kibana or with the Elasticsearch client that Explorer uses:

```php
$client = app(\JeroenG\Explorer\Infrastructure\Elastic\ElasticClientFactory::class)->client();

// The attachment processor expects the file contents as a base64 encoded string in the 'file' field.
$client->ingest()->putPipeline([
    'id' => 'attachment',
    'body' => [
        'description' => 'Extract the text from attached files',
        'processors' => [
            ['attachment' => ['field' => 'file', 'remove_binary' => true]],
        ],
    ],
]);
```

Alternatively, you may let Elasticsearch apply a pipeline to every document of an index by setting the `index.default_pipeline` setting through the [index settings](index-settings.md).
