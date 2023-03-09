# Advanced queries
Explorer expands your possibilities using query builders to write more complex queries.
First there are the three methods to set the context of the query: must, should and filter.

From the Elasticsearch [documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html):

**must**: The query must appear in matching documents and will contribute to the score.

**should**: The query should appear in the matching document. 

**filter**: The query must appear in matching documents. However unlike must the score of the query will be ignored.

Together they take in Explorer a _more-matches-is-better_ approach, the more a document matches with the given queries, the higher its score.
The filter context is best used for structured data, such as dates or flags.

In Explorer, you can build a compound query as complex as you like. Elasticsearch provides a broad set of queries,
these are the query types implementing `SyntaxInterface`. There is for example the `MultiMatch` for fuzzy search and Term for a very precise search.
It is too much to list every type of query here. At the time of writing, Explorer does not yet have every Elasticsearch query type that is out there.
It is however very easy to write a class for a missing query type, and if you do write one a Pull Request is more than welcome!

## Fuzziness
The Matching and MultiMatch queries accept a fuzziness parameter.
By default, it is set to 'auto' but the [Elasticsearch docs](https://www.elastic.co/guide/en/elasticsearch/reference/current/common-options.html#fuzziness) explain in depth which other values you could use.

> "When querying text or keyword fields, fuzziness is interpreted as a Levenshtein Edit Distance - the number of one character changes that need to be made to one string to make it the same as another string."

## Retrieving selected fields
By default Explorer will retrieve all fields for the documents that get returned.
You can change this by using the `field()` function on the search query builder.
It is important to know that this does necessarily have a performance improvement, the whole document is still being processed by Elasticsearch.

> "By default, each hit in the search response includes the document _source, which is the entire JSON object that was provided when indexing the document. To retrieve specific fields in the search response, you can use the fields parameter"
([source](https://www.elastic.co/guide/en/elasticsearch/reference/current/search-fields.html))

```php
use App\Models\Post;

$results = Post::search('Self-steering')
    ->field('id')
    ->field('published_at')
    ->get();
```

The debug json will be like this
```JSON
{
    "query": {
        "bool": {
            "must": [
                {
                    "multi_match": {
                        "query": "Self-steering",
                        "fuzziness": "auto"
                    }
                }
            ],
            "should": [],
            "filter": []
        }
    },
    "fields": [
        "id",
        "published_at"
    ]
}
```

For example, to get all posts that:

are published
have "lorem" somewhere in the document
have "ipsum" in the title
maybe have a tag "featured", if so boost its score by 2
You could execute this search query:

```php
$posts = Post::search('lorem')
    ->must(new Matching('title', 'ipsum'))
    ->should(new Terms('tags', ['featured'], 2))
    ->filter(new Term('published', true))
    ->get();
```

```JSON
{
    "query": {
        "bool": {
            "must": [
                {
                    "match": {
                        "title": {
                            "query": "lorem",
                            "fuzziness": "auto"
                        }
                    }
                },
                {
                    "multi_match": {
                        "query": "lorem",
                        "fuzziness": "auto"
                    }
                }
            ],
            "should": [
                {
                    "terms": {
                        "tags": [
                            "featured"
                        ],
                        "boost": 2
                    }
                }
            ],
            "filter": [
                {
                    "term": {
                        "published": {
                            "value": true,
                            "boost": 1
                        }
                    }
                }
            ]
        }
    }
}
```

If you don not want to multi_match in that case you can set query empty string

```php
$results = Post::search('')
    ->must(new Matching('title', 'lorem'))
    ->should(new Terms('tags', ['featured'], 2))
    ->filter(new Term('published', true))
    ->get();
```

```JSON
{
    "query": {
        "bool": {
            "must": [
                {
                    "match": {
                        "title": {
                            "query": "lorem",
                            "fuzziness": "auto"
                        }
                    }
                }
            ],
            "should": [
                {
                    "terms": {
                        "tags": [
                            "featured"
                        ],
                        "boost": 2
                    }
                }
            ],
            "filter": [
                {
                    "term": {
                        "published": {
                            "value": true,
                            "boost": 1
                        }
                    }
                }
            ]
        }
    }
}
```
