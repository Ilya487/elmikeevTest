<?php

namespace App\Importers;

use App\Api\Client;
use Illuminate\Support\Facades\DB;

abstract class DataImporter
{
    private const int DB_CHUNK_SIZE = 2000;

    public function import(int $startPage = 1, int $requestsPerTime = 10, int $limit = 500)
    {
        $client = $this->getClient();

        foreach ($client->getAll($startPage, $requestsPerTime, $limit) as $data) {
            $chunks = array_chunk($data, self::DB_CHUNK_SIZE);

            DB::transaction(function () use ($chunks) {
                foreach ($chunks as $chunk) {
                    DB::table($this->getTableName())->insert($chunk);
                }
            });
        }
    }

    abstract protected function getClient(): Client;
    abstract protected function getTableName(): string;
}
