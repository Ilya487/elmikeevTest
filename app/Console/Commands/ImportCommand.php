<?php

namespace App\Console\Commands;

use App\Importers\IncomeImporter;
use App\Importers\OrderImporter;
use App\Importers\SaleImporter;
use App\Importers\StockImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('import {entity : incomes, orders, stocks, sales} {--startPage=1}')]
#[Description('Load data from api to db')]
class ImportCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = $this->validateInput();
        $entity = $data['entity'];
        $startPage = (int)$data['startPage'];

        $importer = match ($entity) {
            'incomes' => app(IncomeImporter::class),
            'orders' => app(OrderImporter::class),
            'stocks' => app(StockImporter::class),
            'sales' => app(SaleImporter::class),
            default => throw new \InvalidArgumentException("Unknown entity"),
        };

        $importer->import($startPage);
    }

    private function validateInput(): array|false
    {
        $data = [
            'entity' => $this->argument('entity'),
            'startPage' => $this->option('startPage'),
        ];

        $validator = Validator::make($data, [
            'entity'   => ['required', 'string'],
            'startPage'   => ['required', 'integer', 'min:1'],
        ]);

        return $validator->validate();
    }
}
