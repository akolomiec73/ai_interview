<?php

declare(strict_types=1);

use App\Http\Controllers\DayController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('main');
});

Route::get('/day/{date}', [DayController::class, 'show'])->name('day.show');
