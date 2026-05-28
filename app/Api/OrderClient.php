<?php

namespace App\Api;

class OrderClient extends Client
{
    protected function getEntityName(): string
    {
        return 'orders';
    }
    protected function getQueryFilters(): array
    {
        return  [
            'dateFrom' => '2000-05-01',
            'dateTo' => now()->addDay()->toDateString(),
        ];
    }
}
