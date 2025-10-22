<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class WooCommerceImporter
{
    private Client $http;
    private string $baseUrl = '';
    private string $ck = '';
    private string $cs = '';

    public function __construct(?Client $client = null)
    {
        $this->http = $client ?: new Client(['timeout' => 20]);
        $this->baseUrl = rtrim((string) (config('services.woocommerce.url') ?? ''), '/');
        $this->ck = (string) (config('services.woocommerce.consumer_key') ?? '');
        $this->cs = (string) (config('services.woocommerce.consumer_secret') ?? '');
    }

    public function categories(int $perPage = 100, int $maxPages = 5): array
    {
        $all = [];
        try {
            if (!$this->baseUrl || !$this->ck || !$this->cs) {
                return [];
            }
            for ($page = 1; $page <= $maxPages; $page++) {
                $res = $this->http->get($this->baseUrl.'/wp-json/wc/v3/products/categories', [
                    'query' => [
                        'consumer_key' => $this->ck,
                        'consumer_secret' => $this->cs,
                        'per_page' => $perPage,
                        'page' => $page,
                        'hide_empty' => false,
                    ],
                ]);
                $data = json_decode((string) $res->getBody(), true) ?: [];
                if (empty($data)) { break; }
                $all = array_merge($all, $data);
                if (count($data) < $perPage) { break; }
            }
        } catch (\Throwable $e) {
            \Log::error('WooCommerceImporter categories error', [
                'error' => $e->getMessage(), 'url' => $this->baseUrl
            ]);
            return [];
        }
        return $all;
    }

    public function products(int $perPage = 50, int $maxPages = 10): array
    {
        $all = [];
        try {
            if (!$this->baseUrl || !$this->ck || !$this->cs) {
                return [];
            }
            for ($page = 1; $page <= $maxPages; $page++) {
                $res = $this->http->get($this->baseUrl.'/wp-json/wc/v3/products', [
                    'query' => [
                        'consumer_key' => $this->ck,
                        'consumer_secret' => $this->cs,
                        'per_page' => $perPage,
                        'page' => $page,
                        'status' => 'publish',
                    ],
                ]);
                $data = json_decode((string) $res->getBody(), true) ?: [];
                if (empty($data)) { break; }
                $all = array_merge($all, $data);
                if (count($data) < $perPage) { break; }
            }
        } catch (\Throwable $e) {
            \Log::error('WooCommerceImporter products error', [
                'error' => $e->getMessage(), 'url' => $this->baseUrl
            ]);
            return [];
        }
        return $all;
    }
}
