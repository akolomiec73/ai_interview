<?php

declare(strict_types=1);

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::post('/events', [EventController::class, 'store']);
Route::get('/events', [EventController::class, 'index']);
Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
Route::post('/events/{event}/result-stage', [EventController::class, 'resultStage']);
Route::put('/events/{event}', [EventController::class, 'update']);
Route::get('/upcoming-events', [EventController::class, 'upcoming']);
