<?php

declare(strict_types=1);

namespace App\Repositories\Contract;

use App\DTO\EventDto;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface EventRepositoryInterface
{
    public function createEvent(EventDto $data): Event;

    public function getEvents(Carbon $startDate, Carbon $endDate): Collection;
}
