<?php

namespace App\Importers;

use App\Api\OrderClient;
use App\Models\Order;

class OrderImporter extends DataImporter
{
    public function __construct(private OrderClient $orderClient) {}

    protected function getClient(): \App\Api\Client
    {
        return $this->orderClient;
    }
    protected function getTableName(): string
    {
        return (new Order())->getTable();
    }
}
