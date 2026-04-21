<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DayRequest;
use App\Services\EventService;
use Illuminate\View\View;

class DayController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {}

    public function show(DayRequest $request, string $date): View
    {
        $result = $this->eventService->getEventsForDay($date);

        return view('day', [
            'date' => $result['date'],
            'events' => $result['events'],
        ]);
    }
}
