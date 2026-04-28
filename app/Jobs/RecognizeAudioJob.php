<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Event;
use App\Repositories\Contract\EventRepositoryInterface;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecognizeAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public string $queue = 'parsing';

    protected Event $event;

    protected string $apiKey;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->apiKey = config('yandex-speechkit.api_key');
    }

    public function handle(EventRepositoryInterface $db): void
    {
        try {
            if (! $this->event->audio_path || ! Storage::disk('local')->exists($this->event->audio_path)) {
                throw new Exception('Аудиофайл не найден');
            }

            $tempOggPath = $this->convertAudioFile();

            $audioUrl = $this->uploadFileToBucket($tempOggPath);

            // Удаляем временный OGG файл
            unlink($tempOggPath);

            // API-ключ для SpeechKit
            if (! $this->apiKey) {
                throw new Exception('API-ключ Yandex не задан');
            }

            // Отправляем запрос на асинхронное распознавание
            $recognitionUrl = 'https://transcribe.api.cloud.yandex.net/speech/stt/v2/longRunningRecognize';
            $requestBody = [
                'config' => [
                    'specification' => [
                        'languageCode' => 'ru-RU',
                        'model' => 'general',
                        'audioEncoding' => 'OGG_OPUS',
                        'audioChannelCount' => 2,
                        'sampleRateHertz' => 48000,
                    ],
                    'speakerLabeling' => [
                        'enabled' => true,
                    ],
                ],
                'audio' => [
                    'uri' => $audioUrl,
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Api-Key '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($recognitionUrl, $requestBody);

            if (! $response->successful()) {
                throw new Exception('Ошибка запуска распознавания: '.$response->body());
            }

            $operationId = $response->json('id');

            $db->updateSpeechkitOperationId($operationId, $this->event);

            Log::info('Ожидание завершения операции', ['operation_id' => $operationId]);
            // Ожидаем завершения операции
            $result = $this->waitForOperation($operationId, $this->apiKey);

            // Сохраняем расшифровку
            $this->saveTranscription($result, $db);

            // Удаляем файл из бакета
            $bucketPath = 'audio/'.basename($tempOggPath);
            Storage::disk('yandex')->delete($bucketPath);
        } catch (Exception $e) {
            Log::error('SpeechKit recognition failed', [
                'event_id' => $this->event->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }

    /**
     * Ожидаем завершения операции
     */
    protected function waitForOperation(string $operationId, string $apiKey): array
    {
        $url = "https://operation.api.cloud.yandex.net/operations/{$operationId}";
        $maxAttempts = 180; // 15 минут при паузе 5 секунд
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $response = Http::withHeaders([
                'Authorization' => 'Api-Key '.$apiKey,
            ])->get($url);

            if (! $response->successful()) {
                throw new Exception('Ошибка проверки статуса операции: '.$response->body());
            }

            $data = $response->json();
            $done = $data['done'] ?? false;

            if ($done) {
                if (isset($data['response'])) {
                    return $data['response'];
                } elseif (isset($data['error'])) {
                    throw new Exception('Ошибка в операции: '.json_encode($data['error']));
                } else {
                    throw new Exception('Неизвестный ответ операции');
                }
            }

            $attempt++;
            sleep(5);
        }

        throw new Exception('Превышено время ожидания распознавания');
    }

    /**
     * Сохранение транскрипции
     */
    protected function saveTranscription(array $result, EventRepositoryInterface $db): void
    {
        $transcriptionDetails = [];
        foreach ($result['chunks'] as $chunk) {
            if (isset($chunk['alternatives'][0]['text']) && ! empty($chunk['alternatives'][0]['text'])) {
                $transcriptionDetails[] = [
                    'speaker' => $chunk['channelTag'] ?? null,
                    'text' => $chunk['alternatives'][0]['text'],
                ];
            }
        }
        $db->updateTranscriptionAudio($transcriptionDetails, $this->event);
    }

    /**
     * Конвертирует WebM в OGG Opus через ffmpeg
     */
    protected function convertAudioFile(): string
    {
        $localPath = Storage::disk('local')->path($this->event->audio_path);

        $tempDir = storage_path('app/temp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $tempOggPath = $tempDir.'/'.Str::uuid().'.ogg';

        $command = 'ffmpeg -i '.escapeshellarg($localPath).' -c:a libopus -b:a 48k -ar 48000 -ac 2 '.escapeshellarg($tempOggPath).' 2>&1';
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('FFmpeg conversion failed', ['output' => $output, 'command' => $command]);
            throw new Exception('Ошибка конвертации аудио');
        }

        return $tempOggPath;
    }

    /**
     * Загружаем OGG файл в Yandex Object Storage
     */
    protected function uploadFileToBucket(string $tempOggPath): string
    {
        $bucketPath = 'audio/'.basename($tempOggPath);
        Storage::disk('yandex')->put($bucketPath, fopen($tempOggPath, 'r+'));
        Storage::disk('yandex')->setVisibility($bucketPath, 'public');
        $bucket = config('yandex-speechkit.bucket');

        return "https://storage.yandexcloud.net/{$bucket}/{$bucketPath}";
    }
}
