<?php

namespace App\Api;

class SaleClient extends Client
{
    protected function getEntityName(): string
    {
        return 'sales';
    }
    protected function getQueryFilters(): array
    {
        return [
            'dateFrom' => '2000-05-01',
            'dateTo' => now()->addDay()->toDateString(),
        ];
    }
}
