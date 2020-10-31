<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Infrastructure\Console;

use Illuminate\Console\Command;
use Laravel\Scout\Searchable;

class ElasticSearch extends Command
{
    protected $signature = 'elastic:search
                            {model : Class name of model to bulk import}
                            {term : What to search for}
                            {--fields= : A comma separated list of fields to show in the results}';

    protected $description = 'Search through a particular index.';

    public function handle(): int
    {
        /** @var Searchable $class */
        $class = $this->argument('model');

        $term = $this->argument('term');

        $fields = $this->option('fields') ?? 'id';
        $fields = explode(',', $fields);

        $this->comment("Starting to search for $term");

        $response = $class::search($term);

        $rows = $response->get()->map(function ($model) use ($fields) {
            $row = [];

            foreach ($fields as $field) {
                $row[] = $model->$field;
            }

            return $row;
        });

        $this->table($fields, $rows);

        return 0;
    }
}
