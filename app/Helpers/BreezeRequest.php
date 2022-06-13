<?php

namespace App\Helpers;

use App\Models\ApiLog;
use Illuminate\Support\Facades\Http;

class BreezeRequest
{
    public static function get(string $url, array $headers = [], ?string $provider = null) : array
    {
        $start = time();
        $response = Http::withHeaders($headers)->get($url);
        $data = $response->json();
        $end = time();

        ApiLog::create([
            'url' => $url,
            'provider' => $provider,
            'response' => $data ?? [],
            'request' => [],
            'status' => $response->status(),
            'response_time' => $end - $start
        ]);

        return [
            'status' => $response->status(),
            'data' => $data
        ];
    }

    public static function post(string $url, array $request, array $headers = [], ?string $provider = null) : array
    {
        $start = time();
        $response = Http::withHeaders($headers)->post($url, $request);
        $data = $response->json();
        $end = time();

        ApiLog::create([
            'url' => $url,
            'provider' => $provider,
            'response' => $data ?? [],
            'request' => $request,
            'status' => $response->status(),
            'response_time' => $end - $start
        ]);

        return [
            'status' => $response->status(),
            'data' => $data
        ];
    }
}
