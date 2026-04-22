<?php

declare(strict_types=1);

namespace App\Services\AiProviders\Contracts;

use App\DTO\AnalyzedVacancyDto;

interface AiProviderInterface
{
    /**
     * Анализирует вакансию по URL и возвращает структурированные данные.
     *
     * @return ?array{
     *     company: string,
     *     salary: string,
     *     format_work: string,
     *     skills: string,
     *     topQuestions: array
     * }
     */
    public function analyzeVacancy(string $vacancyUrl): ?AnalyzedVacancyDto;
}
