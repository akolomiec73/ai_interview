<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\AnalyzedVacancyDto;
use App\DTO\CreateEventDto;
use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Vacancy;
use App\Repositories\Contract\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventRepository implements EventRepositoryInterface
{
    /**
     * Создание события
     */
    public function createEvent(CreateEventDto $data): Event
    {
        return Event::create([
            'dateInterview' => $data->dateEvent,
            'linkVacantion' => $data->linkVacancy,
            'comment' => $data->comment,
        ]);
    }

    /**
     * Вывод событий в промежутке с сортировкой
     */
    public function getEvents(Carbon $startDate, Carbon $endDate): Collection
    {
        return Event::with('vacancy')
            ->whereBetween('dateInterview', [$startDate, $endDate])
            ->orderBy('dateInterview', 'asc')
            ->get();
    }

    /**
     * Сохранение анализа вакансии от Ai
     */
    public function createVacancy(string $url, AnalyzedVacancyDto $aiResponse, int $eventId): Vacancy
    {
        $vacancy = Vacancy::create(
            [
                'url' => $url,
                'company' => $aiResponse->company,
                'salary' => $aiResponse->salary,
                'format_work' => $aiResponse->formatWork,
                'skills' => $aiResponse->skills,
                'top_questions' => $aiResponse->topQuestions,
            ]
        );
        Event::where('id', $eventId)->update(['vacancy_id' => $vacancy->id]);

        return $vacancy;
    }

    /**
     * Удаление события и связанной вакансии
     */
    public function deleteEvent(Event $event): void
    {
        if ($event->vacancy) {
            $event->vacancy->delete();
        }
        $event->delete();
    }

    /**
     * Обновление статуса текущего события и добавление следующей стадии
     */
    public function createNextStage(Carbon $newDate, string $comment, Event $event): Event
    {
        return DB::transaction(function () use ($newDate, $comment, $event) {
            $event->update(['status' => EventStatus::Completed]);

            return Event::create([
                'dateInterview' => $newDate,
                'linkVacantion' => $event->linkVacantion,
                'comment' => $comment,
                'status' => EventStatus::Planned,
                'parent_event_id' => $event->id,
                'vacancy_id' => $event->vacancy_id,
            ]);
        });
    }

    /**
     * Обновление даты события
     */
    public function transferEvent(Carbon $newDate, Event $event): void
    {
        $event->update(['dateInterview' => $newDate]);
    }
}
