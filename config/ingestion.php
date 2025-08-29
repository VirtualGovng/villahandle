<?php
return [
    'importers' => [
        'json_importer' => \App\Services\Ingestion\JsonImporter::class,
        // You can keep the old ones here for reference, but they are unreliable
        // 'feature_films' => \App\Services\Ingestion\ArchiveOrgImporter::class,
        // 'tv_series' => \App\Services\Ingestion\ArchiveOrgSeriesImporter::class,
    ]
];