<?php

namespace App\Api;

class ApiConfig
{
    public function __construct(public readonly string $baseUrl, public readonly string $apiKey) {}
}
