<?php

namespace App\Api;

use App\Exceptions\Api\RetrievePageException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

abstract class Client
{
    private readonly string $url;
    private readonly string $apiKey;

    public function __construct()
    {
        $this->url = env('API_HOST') . '/' . $this->getEntityName();
        $this->apiKey = env('API_KEY');
    }

    public function getAll(int $startPage, int $requestPerTime,   int $limitPerRequest = 500)
    {
        $page = $startPage;
        $lastPage = null;

        try {
            while (true) {
                echo "Page $page\n";
                $dataChunk = [];

                $endPage = $lastPage == null
                    ? $page + $requestPerTime - 1
                    : min($page + $requestPerTime - 1, $lastPage);
                $queryParams = $this->genereateQueryParamsForBatchRequests(range($page, $endPage), $limitPerRequest);

                $responses = $this->batchRequest($this->url, $queryParams);
                $responses = $this->handleToManyAttemptsResponse($responses,  $limitPerRequest);

                $emptyPages = 0;
                foreach ($responses as $response) {
                    if ($lastPage === null) {
                        $lastPage = $this->getDataFromResponse($response, 'meta.last_page');
                    }
                    $data = $this->getDataFromResponse($response, 'data');
                    if (empty($data)) {
                        $emptyPages++;
                        continue;
                    }

                    $dataChunk = array_merge($dataChunk, $data);
                }

                if (!empty($dataChunk)) yield $dataChunk;

                if ($lastPage !== null && $endPage >= $lastPage) {
                    break;
                }

                $page = $endPage + 1;
            }
        } catch (\Throwable $th) {
            if (!empty($dataChunk))
                yield $dataChunk;
            throw $th;
        }
    }

    abstract protected function getEntityName(): string;

    abstract protected function getQueryFilters(): array;

    /**
     *@param array<int,\Illuminate\Http\Client\Response> $responses
     *@return array<int,\Illuminate\Http\Client\Response>
     */
    private function handleToManyAttemptsResponse(array $responses, int $limitPerRequest): array
    {
        $successResponses = [];
        $pagesForRestart = [];
        $waitingTime = 0;

        foreach ($responses as $page => $response) {
            if ($response->tooManyRequests()) {
                $pagesForRestart[] = $page;
                $waitingTime = max((int)$response->getHeader('Retry-After')[0] + 1, $waitingTime);
                continue;
            }

            $successResponses[$page] = $response;
        }

        if (empty($pagesForRestart)) return $responses;

        echo 'Limit reach. Wait ' . $waitingTime . ' sec' . PHP_EOL;
        sleep($waitingTime);
        echo 'Wake up' . PHP_EOL;

        $retryResponses = $this->batchRequest($this->url, $this->genereateQueryParamsForBatchRequests($pagesForRestart, $limitPerRequest));
        $res =  $successResponses + $retryResponses;
        ksort($res);

        return $this->handleToManyAttemptsResponse($res, $limitPerRequest);
    }

    private function genereateQueryParamsForBatchRequests(array $pages, int $limitPerRequest)
    {
        $baseQueryParams = [
            ...$this->getQueryFilters(),
            'limit' => $limitPerRequest,
            'key' => $this->apiKey,
        ];

        $res = [];

        foreach ($pages as $page) {
            $res[$page] = array_merge($baseQueryParams, ['page' => $page]);
        }

        return $res;
    }

    /**
     *@return array<int,\Illuminate\Http\Client\Response>
     */
    private function batchRequest(string $url, array $queryParamsArr)
    {
        $requestsCb = function (Pool $pool) use ($queryParamsArr, $url) {
            $requests = [];
            foreach ($queryParamsArr as $page => $params) {
                $requests[] = $pool->as($page)->withQueryParameters($params)->get($url);
            }

            return $requests;
        };

        $responses =  Http::pool($requestsCb);
        ksort($responses);

        return $responses;
    }

    private function getDataFromResponse(\Illuminate\Http\Client\Response|\Throwable $response, string $key)
    {
        if ($response->failed()) {
            dump($response->status());
            $response->dumpHeaders();
            parse_str($response->effectiveUri()->getQuery(), $queryParams);
            $pageNum = $queryParams['page'];
            throw new RetrievePageException($pageNum);
        }

        return $response->json($key);
    }
}
