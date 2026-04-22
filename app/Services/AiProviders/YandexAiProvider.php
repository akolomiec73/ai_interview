<?php

declare(strict_types=1);

namespace App\Services\AiProviders;

use App\DTO\AnalyzedVacancyDto;
use App\Services\AiProviders\Contracts\AiProviderInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

readonly class YandexAiProvider implements AiProviderInterface
{
    public function __construct(
        private string $apiKey,
        private string $folderId,
    ) {}

    /**
     * Анализирует текст вакансии с помощью Yandex AI, т.к. Яндекс не умеет ходить по ссылкам, отдаём текст страницы
     *
     * @throws ConnectionException
     */
    public function analyzeVacancy(string $vacancyUrl): ?AnalyzedVacancyDto
    {
        $prompt = $this->buildPrompt($vacancyUrl);

        $response = Http::timeout(120)
            ->withHeaders([
                'Authorization' => 'Api-Key '.$this->apiKey,
                'x-folder-id' => $this->folderId,
                'Content-Type' => 'application/json',
            ])
            ->post('https://llm.api.cloud.yandex.net/foundationModels/v1/completion', [
                'modelUri' => "gpt://{$this->folderId}/yandexgpt/latest",
                'completionOptions' => [
                    'stream' => false,
                    'temperature' => 0.7,
                    'maxTokens' => 2000,
                ],
                'messages' => [
                    ['role' => 'system', 'text' => 'Ты — ассистент, который извлекает структурированную информацию из текста вакансии.'],
                    ['role' => 'user', 'text' => $prompt],
                ],
            ]);

        if ($response->failed()) {
            Log::error('Yandex AI API error', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        }

        $result = $response->json();
        $content = $result['result']['alternatives'][0]['message']['text'] ?? '';

        return $this->parseResponse($content);
    }

    /**
     * Отдаёт промпт с текстом страницы
     *
     * @throws ConnectionException
     */
    private function buildPrompt(string $url): string
    {
        $text = $this->getTextFromUrl($url);

        return "Проанализируй следующий текст вакансии и верни результат строго в формате JSON:, где:
                    company - Название компании-работодателя,
                    salary - Информация о зарплате,
                    formatWork - Формат работы (например: 'Удаленно', 'Гибрид', 'В офисе' или 'Не указано'),
                    skills - Ключевые требования к Hard skills через запятую,
                    topQuestions - массив из 10 наиболее вероятных технических вопросов для подготовки к собеседованию по этой вакансии
                Правила:
                    - Если поле не найдено, используй значение 'Не указано'.
                    - Для поля 'salary':
                        - Если указан диапазон (например, '100 000 – 150 000 руб.'), верни верхнюю границу в формате 'до 150 000 руб.'.
                        - Если указана только минимальная планка (например, 'от 120 000 руб.'), верни минимальное значение 'от 120 000 руб.').
                        - Если число не указано, верни строку 'Не указано'.
                    - Для поля 'skills': верни их в виде строки, перечисляя только конкретные технологии и инструменты.
                    - Для поля 'topQuestions': верни массив из 10 конкретных технических вопросов по стеку вакансии.
                        Запрещены общие фразы 'как вы используете', 'расскажите об опыте', 'что вы знаете'.
                        Каждый вопрос должен требовать объяснения механизма, сравнения, примера кода.
                        Примеры правильных вопросов:
                        1. 'Какие типы индексов в MySQL поддерживаются и в каком сценарии следует использовать хеш-индекс?'
                        2. 'Как в PHP реализовать трейт с абстрактным методом и каковы ограничения такого подхода?'
                    - Не добавляй никаких комментариев к своему ответу. Твой ответ должен содержать только валидный JSON. ".$text;
    }

    /**
     * Извлекает текст по ссылке
     *
     * @throws ConnectionException
     */
    private function getTextFromUrl(string $url): string
    {
        $response = Http::withOptions(['verify' => false])->get($url);
        $html = $response->body();
        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);
        // Удаляем все элементы script и style ...
        foreach ($xpath->query('//script | //style | //meta | //link | //noscript') as $node) {
            $node->parentNode->removeChild($node);
        }
        // Ищем div с классом "row-content"
        $nodes = $xpath->query('//div[contains(@class, "row-content")]');
        $description = $nodes->item(0)->textContent;
        $description = preg_replace('/\s+/', ' ', $description);

        return trim($description);
    }

    /**
     * Парсит ответ, извлекая json
     */
    private function parseResponse(string $content): ?AnalyzedVacancyDto
    {
        $content = preg_replace('/^```json\s*|\s*```$/m', '', $content);
        $content = trim($content);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response', ['content' => $content]);

            return null;
        }

        return AnalyzedVacancyDto::fromArray($data);

    }
}
