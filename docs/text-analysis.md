# Text analysis
Text analysis is set as part of the [index settings](index-settings.md).

The following example creates a synonym analyzer, the end result would be that when you search for 'Vue' you (also) get the results for 'React'.
To make sure the synonyms match all cases, the `lowercase` filter is run as well.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
use JeroenG\Explorer\Domain\Analysis\Analysis;
use JeroenG\Explorer\Domain\Analysis\Analyzer\StandardAnalyzer;
use JeroenG\Explorer\Domain\Analysis\Filter\SynonymFilter;
use Laravel\Scout\Searchable;

class Post extends Model implements Explored, IndexSettings
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['title', 'published'];

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'title' => [
                'type' => 'text',
                'analyzer' => 'frameworks',
            ],
            'published' => 'boolean',
            'created_at' => 'date',
        ];
    }
    
    public function indexSettings(): array
    {
        $synonymFilter = new SynonymFilter();
        $synonymFilter->setSynonyms(['vue => react']);

        $synonymAnalyzer = new StandardAnalyzer('frameworks');
        $synonymAnalyzer->setFilters(['lowercase', $synonymFilter]);

        return (new Analysis())
            ->addAnalyzer($synonymAnalyzer)
            ->addFilter($synonymFilter)
            ->build();
    }
}
```

It is very easy to create synonym filters and analyzers, but be aware that they are 'expensive' to run for Elasticsearch.
Before turning to synonyms, see if you can use wildcards or fuzzy queries.

## Interesting references
- [official analysis documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis.html)
- [testing analyzers](https://www.elastic.co/guide/en/elasticsearch/reference/current/test-analyzer.html)
