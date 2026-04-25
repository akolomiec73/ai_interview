<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Vacancy;
use App\Services\AiProviderFactory;
use Illuminate\Console\Command;

class RefreshVacanciesData extends Command
{
    protected $signature = 'vacancies:refresh
                            {--ids=* : Список ID вакансий для обновления (если не указаны, обновляются все)}
                            {--limit=100 : Количество вакансий за один запуск}';

    protected $description = 'Повторно анализирует вакансии через AI и обновляет новые поля';

    public function handle()
    {
        $ids = $this->option('ids');
        $limit = (int) $this->option('limit');

        $query = Vacancy::query();
        if (! empty($ids)) {
            $query->whereIn('id', $ids);
        }
        $vacancies = $query->limit($limit)->get();

        if ($vacancies->isEmpty()) {
            $this->error('Нет вакансий для обновления.');

            return;
        }

        $providerName = config('services.ai.default_provider');
        $ai = AiProviderFactory::make($providerName);

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
            // Небольшая задержка, чтобы не превысить лимиты API
            usleep(500000); // 0.5 секунды
        }
        $bar->finish();
        $this->newLine();
        $this->info('Обновление завершено.');
    }
}
