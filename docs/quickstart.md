# Quickstart

Elasticsearch is "a distributed, open source search and analytics engine for all types of data" using REST and JSON.
The key concepts being are the index (a collection of documents), full-text searches and data ingestion (the processing of raw data).
Kibana is the interface to visualise and navigate through your elasticsearch indexes.

A very good way to get started with Elasticsearch is by creating a docker container and connecting it to your Laravel application.
You could use Tighten's Takeout for the Elasticsearch container, however it comes without Kibana.
To get started with Elasticsearch and Kibana, create a `docker-compose.yml` file and paste this configuration from [Elastic's documentation](https://www.elastic.co/guide/en/elastic-stack-get-started/current/get-started-docker.html).
After that, run `docker-compose up -d` and you will have an elasticsearch instance running at localhost:9200 and a Kibana instance at localhost:5601

```yaml
version: '2.2'
services:
  es01:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.9.3
    container_name: es01
    environment:
      - node.name=es01
      - cluster.name=es-docker-cluster
      - discovery.type=single-node
      - bootstrap.memory_lock=true
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - data01:/usr/share/elasticsearch/data
    ports:
      - 9200:9200
    networks:
      - elastic

  kib01:
    image: docker.elastic.co/kibana/kibana:7.9.3
    container_name: kib01
    ports:
      - 5601:5601
    environment:
      ELASTICSEARCH_URL: http://es01:9200
      ELASTICSEARCH_HOSTS: http://es01:9200
    networks:
      - elastic

volumes:
  data01:
    driver: local

networks:
  elastic:
    driver: bridge

```

The thing you should do next, if you haven't already, is follow the [Laravel Scout](https://laravel.com/docs/scout) installation and configuration.
The only point where you should diverge from the docs is the driver for scout (in config/scout.php):

```php
'driver' => 'elastic',
```

After that, you can define your first index in config/explorer.php:

```php
'indexes' => [
    'posts_index' => [
        'properties' => [
            'id' => 'keyword',
            'title' => 'text',
        ],
    ]
]
```

Upon saving the file, run `php artisan scout:import "App\Models\Post"` to add your posts as documents to the index.
This of course assumes that you have a Post model (with an ID and title attribute) and a couple of entries of them in your database.
As mentioned before, Laravel Scout also has a few requirements explained in its documentation for your models.

To query your posts, use Scout's search method to find stuff, for example:

```php
$ipsum = App\Models\Post::search('Lorem')->get();
```

Enjoy!
