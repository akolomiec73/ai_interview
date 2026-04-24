<?php

declare(strict_types=1);

use App\Http\Controllers\DayController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('main'))->name('main');
Route::get('/day/{date}', [DayController::class, 'show'])->name('day.show');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
