<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\AiProviders\Contracts\AiProviderInterface;
use App\Services\AiProviders\OpenAiProvider;
use App\Services\AiProviders\YandexAiProvider;
use InvalidArgumentException;

/**
 * Фабрика определения AI провайдера из env
 */
class AiProviderFactory
{
    public static function make(string $provider): AiProviderInterface
    {
        return match ($provider) {
            'yandex' => new YandexAiProvider(
                apiKey: config('services.yandex.api_key'),
                folderId: config('services.yandex.folder_id')
            ),
            'openai' => new OpenAiProvider(
                apiKey: config('services.openai.api_key')
            ),
            default => throw new InvalidArgumentException("Неизвестный AI: {$provider}"),
        };
    }
}
