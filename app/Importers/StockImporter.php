<?php

namespace App\Importers;

use App\Api\StockClient;
use App\Models\Stock;

class StockImporter extends DataImporter
{
    public function __construct(private StockClient $stockClient) {}

    protected function getClient(): \App\Api\Client
    {
        return $this->stockClient;
    }
    protected function getTableName(): string
    {
        return (new Stock())->getTable();
    }
}
