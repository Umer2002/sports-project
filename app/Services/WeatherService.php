<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5/weather';

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
    }

    public function getCurrentWeather($city = 'Ottawa', $country = 'CA')
    {
        $cacheKey = "weather_{$city}_{$country}";

        return Cache::remember($cacheKey, 1800, function () use ($city, $country) {
            try {
                $response = Http::get($this->baseUrl, [
                    'q' => "{$city},{$country}",
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'temp' => round($data['main']['temp']),
                        'temp_min' => round($data['main']['temp_min']),
                        'temp_max' => round($data['main']['temp_max']),
                        'condition' => $data['weather'][0]['main'],
                        'icon' => $data['weather'][0]['icon'],
                        'city' => $city,
                        'country' => $country
                    ];
                }

                return null;
            } catch (\Exception $e) {
                \Log::error('Weather API Error: ' . $e->getMessage());
                return null;
            }
        });
    }
} 