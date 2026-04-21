<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

readonly class EventDto
{
    /**
     * @param  Carbon  $dateInterview  дата собеса
     * @param  string  $linkVacantion  ссылка на вакансию
     * @param  ?string  $comment  комментарий
     */
    public function __construct(
        public Carbon $dateInterview,
        public string $linkVacantion,
        public ?string $comment,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            dateInterview: Carbon::parse($data['dateInterview']),
            linkVacantion: $data['linkVacantion'],
            comment: $data['comment'] ?? null,
        );
    }
}
