<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\MetricsController;
use Illuminate\Support\Facades\Route;

// POST-запрос формы с защитой throttle (ограничение запросов в минуту на один IP)
Route::post('/contact', [ContactController::class, 'submit'])
    ->middleware('throttle:'.env('REQUESTS_RATE_LIMIT', 5).',1');

// Дополнительные эндпоинты
Route::get('/health', [HealthController::class, 'health']);
Route::get('/metrics', [MetricsController::class, 'metrics']);
