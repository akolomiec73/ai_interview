<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Carbon;

readonly class ResultStageDto
{
    /**
     * @param  string  $action  тип действия
     * @param  ?string  $comment  комментарий
     * @param  ?Carbon  $dateEvent  комментарий
     * @param  ?string  $eventStage  стадия
     */
    public function __construct(
        public string $action,
        public ?string $comment,
        public ?Carbon $dateEvent,
        public ?string $eventStage,
    ) {}

    public static function fromArray(array $data): self
    {
        $dateEvent = null;
        if (isset($data['dateEvent']) && $data['dateEvent']) {
            $dateEvent = Carbon::parse($data['dateEvent']);
        }

        return new self(
            action: $data['action'],
            comment: $data['comment'] ?? null,
            dateEvent: $dateEvent,
            eventStage: $data['eventStage'] ?? null,
        );
    }
}
