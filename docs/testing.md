#Testing
The separation of code in this package between Application, Domain and Infrastructure make it easy to (unit) test how your app works with it.
When you want to test the integration of your app with this Explorer, it can help you with mocking the raw responses Elasticsearch may give.
It does not fully test the integration with Elasticsearch, but it does allow testing without having a live Elasticsearch instance.
The responses that the Elasticsearch client returns are replaced with fake responses at a low level, meaning that the rest of the application does not need to work with mocks.

Here is an example of a [Laravel feature test](https://laravel.com/docs/testing):

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use JeroenG\Explorer\Infrastructure\Elastic\FakeResponse;
use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    public function test_it_returns_results(): void
    {
        $fakeResponseFile = fopen(base_path("tests/Support/Elastic/Responses/example.json"), 'rb');
        
        $fakeResponse = new FakeResponse($statusCode, $fakeResponseFile);
        
        // see https://laravel.com/docs/mocking for more information
        $this->instance(ElasticClientFactory::class, ElasticClientFactory::fake($fakeResponse));

        $response = $this->post('api/search');

        $response->assertStatus(200);
    }
}
```

You can see an example of a raw Elastic response in the tests/Support folder of Explorer.

## Making changes immediately searchable

Elasticsearch makes indexed and removed documents visible to search on its next periodic refresh, not right away.
When a test (or any other code) searches directly after saving or deleting a model, the result may still reflect the old state.

Models are indexed and removed through the Elasticsearch bulk API, so you can set the `refresh` parameter of those requests with the `bulk_refresh` option in `config/explorer.php` or the `EXPLORER_ELASTIC_BULK_REFRESH` environment variable:

- `true` refreshes the affected shards right after every bulk request.
- `wait_for` waits until the next refresh has made the changes visible before the request returns.
- `false` (the default) does not wait for a refresh.

For example, in your `phpunit.xml`:

```xml
<env name="EXPLORER_ELASTIC_BULK_REFRESH" value="wait_for"/>
```

Refreshing has a performance impact, so it is best not to enable it in production unless you need to read your own writes.
