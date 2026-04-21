<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EventIndexRequest;
use App\Http\Requests\EventRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {}

    public function store(EventRequest $request): JsonResponse
    {
        $this->eventService->addEvent($request->toDto());

        return response()->json(['message' => 'Элемент успешно создан'], 201);

    }

    public function index(EventIndexRequest $request): JsonResponse
    {
        return response()->json($this->eventService->getEventsForMonth((int) $request->year, (int) $request->month));
    }
}
