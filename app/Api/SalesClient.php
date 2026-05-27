<?php

namespace App\Api;

class SalesClient extends Client
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
