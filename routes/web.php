<?php

use App\Http\Controllers\TwilioController;
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

// Twilio voice webhooks
Route::prefix('twilio')->name('twilio.')->group(function () {
    Route::post('/inbound', [TwilioController::class, 'handleInbound'])->name('inbound');
    Route::post('/process-speech', [TwilioController::class, 'processSpeech'])->name('process-speech');
    Route::post('/status-callback', [TwilioController::class, 'handleStatusCallback'])->name('status-callback');
});
