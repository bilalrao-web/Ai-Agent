<?php

use App\Http\Controllers\TwilioController;
use App\Services\QueryProcessorService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('twilio')->name('twilio.')->group(function () {
    Route::post('/inbound', [TwilioController::class, 'handleInbound'])->name('inbound');
    Route::match(['get', 'post'], '/process-speech', [TwilioController::class, 'processSpeech'])->name('process-speech');
    Route::post('/status-callback', [TwilioController::class, 'handleStatusCallback'])->name('status-callback');
});
