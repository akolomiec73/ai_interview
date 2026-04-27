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

        return "Проанализируй следующий текст вакансии и верни результат строго в формате JSON, где:
                    - company - Название компании-работодателя.
                    - jobTitle - Название должности (например, 'PHP Developer', 'Team Lead', 'Project Manager').
                    - city - Город, где находится офис.
                    - industry - Сфера деятельности компании (например, 'Финтех', 'E-commerce', 'Логистика', 'Медицина').
                    - salary - Информация о зарплате (см. правила ниже).
                    - formatWork - Формат работы: 'Удаленно', 'Гибрид', 'В офисе' или 'Не указано'.
                    - skills - Ключевые требования к Hard skills через запятую.
                    - topQuestions - массив из 10 наиболее вероятных технических вопросов для подготовки к собеседованию по этой вакансии.
                    - benefits - Условия работы (строка): выдели не более 5 наиболее ценных и уникальных пунктов
                        (например, 'ДМС', 'корпоративный ноут', 'ежегодная премия 15%', 'оплата курсов', 'гибкий график').
                        Исключай общие фразы: 'оформление по ТК', 'оплачиваемый отпуск', 'карьерный рост', 'профессиональный рост',
                        'дружный коллектив', 'безлимитный кофе', 'зона отдыха', 'настольные игры', 'пицца по пятницам',
                        'семинары', 'наставничество'. Максимальная длина строки – 200 символов.
                        Если реальных бонусов нет, верни 'Не указано'.

                    Правила:
                    - Если не смог определить информацию для поля, используй значение 'Не указано'. Не придумывай ничего.
                    - Для salary:
                        - Если диапазон (например, '100 000 – 150 000 руб.') → 'до 150 000 руб.'
                        - Если только минимальная планка (например, 'от 120 000 руб.') → 'от 120 000 руб.'
                        - Иначе 'Не указано'.
                    - skills: только конкретные технологии, инструменты.
                    - topQuestions: 10 конкретных технических вопросов по стеку вакансии.
                        Запрещены общие фразы 'как вы используете', 'расскажите об опыте', 'что вы знаете'.
                        Каждый вопрос должен требовать объяснения механизма, сравнения или примера кода.
                    - Не добавляй никаких комментариев. Ответ должен содержать только валидный JSON.

                    Текст вакансии:
                    {$text}";
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
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Вспомогательная функция для получения текста по XPath
        $getText = function (string $query) use ($xpath): string {
            $nodes = $xpath->query($query);
            if ($nodes && $nodes->length > 0) {
                return trim($nodes->item(0)->textContent);
            }

            return '';
        };

        // Извлечение данных
        $company = $getText('//span[contains(@class, "vacancy-company-name")]');
        $title = $getText('//div[contains(@class, "vacancy-title")]');
        $address = $getText('//span[@data-qa="vacancy-view-raw-address"]');
        $format = $getText('//p[@data-qa="work-formats-text"]');
        $desc = $getText('//div[@data-qa="vacancy-description"]');

        // Очистка
        $company = preg_replace('/\s+/', ' ', $company);
        $title = preg_replace('/\s+/', ' ', $title);
        $address = preg_replace('/\s+/', ' ', $address);
        $format = preg_replace('/\s+/', ' ', $format);
        $desc = strip_tags($desc);
        $desc = preg_replace('/\s+/', ' ', trim($desc));

        // Формируем итоговый текст для AI
        $result = "Название компании: $company\n";
        $result .= "Заголовок вакансии: $title\n";
        $result .= "Адрес: $address\n";
        $result .= "$format\n";
        $result .= "Описание:\n$desc\n";

        return trim($result);
    }

    /**
     * Парсит ответ, извлекая json
     */
    private function parseResponse(string $content): ?AnalyzedVacancyDto
    {
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', $content);
        $content = trim($content);
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse AI response', ['content' => $content]);

            return null;
        }

        return AnalyzedVacancyDto::fromArray($data);

    }
}
