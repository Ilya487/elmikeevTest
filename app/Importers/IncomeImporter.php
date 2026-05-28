<?php

namespace App\Importers;

use App\Api\IncomeClient;
use App\Models\Income;

class IncomeImporter extends DataImporter
{
    public function __construct(private IncomeClient $incomeClient) {}

    protected function getClient(): \App\Api\Client
    {
        return $this->incomeClient;
    }
    protected function getTableName(): string
    {
        return (new Income())->getTable();
    }
}
