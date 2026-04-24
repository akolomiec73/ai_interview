<?php

declare(strict_types=1);

namespace App\Repositories\Contract;

use App\DTO\AnalyzedVacancyDto;
use App\DTO\EventDto;
use App\Models\Event;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface EventRepositoryInterface
{
    public function createEvent(EventDto $data): Event;

    public function getEvents(Carbon $startDate, Carbon $endDate): Collection;

    public function createVacancy(string $url, AnalyzedVacancyDto $aiResponse, int $eventId): Vacancy;

    public function deleteEvent(Event $event): void;
}
