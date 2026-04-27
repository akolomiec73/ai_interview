<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Vacancy;
use App\Services\AiProviderFactory;
use Illuminate\Console\Command;

class RefreshVacanciesData extends Command
{
    protected $signature = 'vacancies:refresh
                            {--ids=* : Список ID вакансий для обновления}
                            {--event-ids=* : Список ID событий, для которых нужно обновить/создать связанные вакансии по ссылке}
                            {--limit=100 : Количество обрабатываемых записей за один запуск}';

    protected $description = 'Анализирует вакансии через AI и обновляет/создаёт записи в таблице vacancies';

    public function handle()
    {
        $ids = $this->option('ids');
        $eventIds = $this->option('event-ids');
        $limit = (int) $this->option('limit');

        $providerName = config('services.ai.default_provider');
        $ai = AiProviderFactory::make($providerName);

        // Режим работы: по ID вакансий
        if (! empty($ids)) {
            $this->updateVacanciesByIds($ids, $limit, $ai);
        }
        // Режим работы: по ID событий
        elseif (! empty($eventIds)) {
            $this->updateVacanciesByEventIds($eventIds, $limit, $ai);
        }
        // Ничего не указано – обновляем все вакансии (по старинке)
        else {
            $this->updateAllVacancies($limit, $ai);
        }
    }

    private function updateVacanciesByIds(array $ids, int $limit, $ai): void
    {
        $vacancies = Vacancy::whereIn('id', $ids)->limit($limit)->get();
        $this->processVacancies($vacancies, $ai);
    }

    private function updateAllVacancies(int $limit, $ai): void
    {
        $vacancies = Vacancy::limit($limit)->get();
        $this->processVacancies($vacancies, $ai);
    }

    private function updateVacanciesByEventIds(array $eventIds, int $limit, $ai): void
    {
        $events = Event::whereIn('id', $eventIds)
            ->whereNotNull('linkVacantion')
            ->limit($limit)
            ->get();

        if ($events->isEmpty()) {
            $this->error('Для переданных событий не найдено ни одной ссылки на вакансию.');

            return;
        }

        $bar = $this->output->createProgressBar($events->count());
        $bar->start();

        foreach ($events as $event) {
            $bar->advance();
            try {
                $url = $event->linkVacantion;
                $this->info("\nАнализ вакансии по URL: $url");

                $result = $ai->analyzeVacancy($url);
                if ($result === null) {
                    $this->warn("Не удалось проанализировать вакансию для события ID {$event->id}");

                    continue;
                }

                // Поиск существующей вакансии по URL
                $vacancy = Vacancy::firstOrCreate(
                    ['url' => $url],
                    [
                        'company' => $result->company,
                        'salary' => $result->salary,
                        'format_work' => $result->formatWork,
                        'skills' => $result->skills,
                        'top_questions' => $result->topQuestions,
                        'job_title' => $result->jobTitle,
                        'city' => $result->city,
                        'industry' => $result->industry,
                        'benefits' => $result->benefits,
                    ]
                );

                // Если вакансия уже существовала, обновляем её данными от AI
                if (! $vacancy->wasRecentlyCreated) {
                    $vacancy->update([
                        'company' => $result->company ?? $vacancy->company,
                        'salary' => $result->salary ?? $vacancy->salary,
                        'format_work' => $result->formatWork ?? $vacancy->format_work,
                        'skills' => $result->skills ?? $vacancy->skills,
                        'top_questions' => $result->topQuestions ?? $vacancy->top_questions,
                        'job_title' => $result->jobTitle ?? $vacancy->job_title,
                        'city' => $result->city ?? $vacancy->city,
                        'industry' => $result->industry ?? $vacancy->industry,
                        'benefits' => $result->benefits ?? $vacancy->benefits,
                    ]);
                }

                // Привязываем вакансию к событию, если ещё не привязана
                if ($event->vacancy_id !== $vacancy->id) {
                    $event->vacancy_id = $vacancy->id;
                    $event->save();
                    $this->info("Событие ID {$event->id} связано с вакансией ID {$vacancy->id}");
                }

            } catch (\Exception $e) {
                $this->error("Ошибка для события ID {$event->id}: {$e->getMessage()}");
            }
            usleep(500000);
        }
        $bar->finish();
        $this->newLine();
        $this->info('Обновление по событиям завершено.');
    }

    private function processVacancies($vacancies, $ai): void
    {
        if ($vacancies->isEmpty()) {
            $this->error('Нет вакансий для обновления.');

            return;
        }

        $bar = $this->output->createProgressBar($vacancies->count());
        $bar->start();

        foreach ($vacancies as $vacancy) {
            $bar->advance();
            try {
                $result = $ai->analyzeVacancy($vacancy->url);
                if ($result === null) {
                    $this->warn("Не удалось проанализировать вакансию ID {$vacancy->id}");

                    continue;
                }

                $vacancy->update([
                    'company' => $result->company ?? $vacancy->company,
                    'salary' => $result->salary ?? $vacancy->salary,
                    'format_work' => $result->formatWork ?? $vacancy->format_work,
                    'skills' => $result->skills ?? $vacancy->skills,
                    'top_questions' => $result->topQuestions ?? $vacancy->top_questions,
                    'job_title' => $result->jobTitle ?? $vacancy->job_title,
                    'city' => $result->city ?? $vacancy->city,
                    'industry' => $result->industry ?? $vacancy->industry,
                    'benefits' => $result->benefits ?? $vacancy->benefits,
                ]);
            } catch (\Exception $e) {
                $this->error("Ошибка для вакансии {$vacancy->id}: {$e->getMessage()}");
            }
            usleep(500000);
        }
        $bar->finish();
        $this->newLine();
        $this->info('Обновление завершено.');
    }
}
