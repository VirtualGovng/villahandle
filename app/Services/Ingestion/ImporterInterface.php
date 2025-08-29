<?php

namespace App\Services\Ingestion;

interface ImporterInterface
{
    /**
     * Fetches the list of content from the external source.
     * @return array
     */
    public function fetch(): array;

    /**
     * Processes a single item from the fetched list.
     * @param array $item
     */
    public function process(array $item): void;
}