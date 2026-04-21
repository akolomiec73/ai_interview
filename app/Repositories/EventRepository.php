<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\EventDto;
use App\Models\Event;
use App\Repositories\Contract\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class EventRepository implements EventRepositoryInterface
{
    /**
     * Создание события
     */
    public function createEvent(EventDto $data): Event
    {
        return Event::create([
            'dateInterview' => $data->dateInterview,
            'linkVacantion' => $data->linkVacantion,
            'comment' => $data->comment,
        ]);
    }

    /**
     * Вывод событий в промежутке с сортировкой
     */
    public function getEvents(Carbon $startDate, Carbon $endDate): Collection
    {
        return Event::whereBetween('dateInterview', [$startDate, $endDate])
            ->select('id', 'dateInterview', 'linkVacantion', 'comment')
            ->orderBy('dateInterview', 'asc')
            ->get();
    }
}
