<?php

namespace App\Importers;

use App\Api\SaleClient;
use App\Models\Sale;

class SaleImporter extends DataImporter
{
    public function __construct(private SaleClient $saleClient) {}

    protected function getClient(): \App\Api\Client
    {
        return $this->saleClient;
    }
    protected function getTableName(): string
    {
        return (new Sale())->getTable();
    }
}
