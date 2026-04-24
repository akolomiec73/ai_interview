<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateNextStageRequest;
use App\Http\Requests\EventIndexRequest;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

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

    public function show(Event $event): View
    {
        $event->load('vacancy');

        return view('event', compact('event'));
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->eventService->deleteEvent($event);

        return response()->json(['message' => 'Элемент успешно удалён']);
    }

    public function createNextStage(CreateNextStageRequest $request, Event $event): JsonResponse
    {
        try {
            $newDate = Carbon::parse($request->dateInterview);

            $newEvent = $this->eventService->createNextStage($newDate, $request->comment, $event);

            return response()->json(['message' => 'Следующий этап создан', 'event' => $newEvent], 201);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
