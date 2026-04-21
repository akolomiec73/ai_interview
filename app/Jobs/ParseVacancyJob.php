<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseVacancyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public string $queue = 'parsing';

    public function __construct(
        public string $vacancyUrl,
    ) {}

    public function handle(): void
    {
        Log::info('ParseVacancyJob started', ['url' => $this->vacancyUrl]);

        // Здесь будет логика парсинга (пока пусто)

        Log::info('ParseVacancyJob finished', ['url' => $this->vacancyUrl]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('ParseVacancyJob failed', [
            'url' => $this->vacancyUrl,
            'error' => $e->getMessage(),
        ]);
    }
}
