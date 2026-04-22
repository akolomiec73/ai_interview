<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTO\AnalyzedVacancyDto;
use App\DTO\EventDto;
use App\Models\Event;
use App\Models\Vacancy;
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
        \Log::info('try update key', ['eventId' => $eventId, 'vacancyId' => $vacancy->id]);

        return $vacancy;
    }
}
