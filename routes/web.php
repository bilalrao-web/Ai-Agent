<?php

use App\Services\QueryProcessorService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// AI Demo page – dropdown se query select karke Gemini response
Route::get('/ai-demo', function () {
    return view('ai-demo');
});

// API: process query (Gemini + tool calling), returns JSON
Route::get('/test-ai/{queryType}/{customerId}', function (string $queryType, int $customerId, QueryProcessorService $processor) {
    $result = $processor->process($queryType, $customerId);
    return response()->json($result);
});
