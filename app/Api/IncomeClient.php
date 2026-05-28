<?php

namespace App\Api;

class IncomeClient extends Client
{
    protected function getEntityName(): string
    {
        return 'incomes';
    }
    protected function getQueryFilters(): array
    {
        return [
            'dateFrom' => '2000-05-01',
            'dateTo' => now()->addDay()->toDateString(),
        ];
    }
}
