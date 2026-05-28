<?php

namespace App\Api;

use Illuminate\Support\Carbon;

class StockClient extends Client
{
    protected function getEntityName(): string
    {
        return 'stocks';
    }
    protected function getQueryFilters(): array
    {
        return  [
            'dateFrom' => Carbon::yesterday()->plus(days: 1)->toDateString(),
        ];
    }
}
