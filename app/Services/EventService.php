<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\EventDto;
use App\Jobs\ParseVacancyJob;
use App\Repositories\Contract\EventRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EventService
{
    public function __construct(
        private EventRepositoryInterface $db,
    ) {}

    /**
     * Создание события
     */
    public function addEvent(EventDto $data): void
    {
        $event = $this->db->createEvent($data);
        ParseVacancyJob::dispatch($data->linkVacantion, $event->id);
    }

    /**
     * Получение событий за день
     */
    public function getEventsForDay(string $date): array
    {
        $carbonDate = Carbon::parse($date);
        $startOfDay = $carbonDate->copy()->startOfDay();
        $endOfDay = $carbonDate->copy()->endOfDay();

        $events = $this->db->getEvents($startOfDay, $endOfDay);

        return [
            'date' => $carbonDate,
            'events' => $events,
        ];
    }

    /**
     * Получение событий за месяц
     */
    public function getEventsForMonth(int $year, int $month): Collection
    {
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        $events = $this->db->getEvents($startDate, $endDate);

        return $events->groupBy(fn ($e) => $e->dateInterview->format('Y-m-d'));
    }
}
