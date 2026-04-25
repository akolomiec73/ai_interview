<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

readonly class CreateEventDto
{
    /**
     * @param  Carbon  $dateEvent  дата события
     * @param  string  $linkVacancy  ссылка на вакансию
     * @param  ?string  $comment  комментарий
     */
    public function __construct(
        public Carbon $dateEvent,
        public string $linkVacancy,
        public ?string $comment,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            dateEvent: Carbon::parse($data['dateInterview']),
            linkVacancy: $data['linkVacantion'],
            comment: $data['comment'] ?? null,
        );
    }
}
