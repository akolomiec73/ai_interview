<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Repositories\Contract\EventRepositoryInterface;
use App\Services\AiProviderFactory;
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
        public int $eventId,
    ) {}

    public function handle(EventRepositoryInterface $db): void
    {
        Log::info('ParseVacancyJob started for event_id = '.$this->eventId, ['url' => $this->vacancyUrl]);

        $providerName = config('services.ai.default_provider');
        $provider = AiProviderFactory::make($providerName);

        $result = $provider->analyzeVacancy($this->vacancyUrl);

        $db->createVacancy($this->vacancyUrl, $result, $this->eventId);

    }

    public function failed(\Throwable $e): void
    {
        Log::error('ParseVacancyJob failed', [
            'url' => $this->vacancyUrl,
            'error' => $e->getMessage(),
        ]);
    }
}
