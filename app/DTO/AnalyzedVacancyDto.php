<?php

declare(strict_types=1);

namespace App\DTO;

readonly class AnalyzedVacancyDto
{
    public function __construct(
        public string $company,
        public string $salary,
        public string $formatWork,
        public string $skills,
        public array $topQuestions,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            company: $data['company'] ?? 'Не указано',
            salary: $data['salary'] ?? 'Не указано',
            formatWork: $data['formatWork'] ?? 'Не указано',
            skills: $data['skills'] ?? '',
            topQuestions: $data['topQuestions'] ?? [],
        );
    }
}
