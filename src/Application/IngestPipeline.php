<?php

declare(strict_types=1);

namespace JeroenG\Explorer\Application;

interface IngestPipeline
{
    /** The name of the Elasticsearch ingest pipeline the document is processed with before it is indexed. */
    public function ingestPipeline(): string;
}
