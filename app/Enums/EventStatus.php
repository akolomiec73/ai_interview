<?php

namespace App\Enums;

enum EventStatus: string
{
    case Planned = 'planned';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Transferred = 'transferred';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Запланировано',
            self::Completed => 'Пройдено',
            self::Cancelled => 'Отменено',
            self::Transferred => 'Перенесено',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planned => 'status-planned',
            self::Completed => 'status-completed',
            self::Cancelled => 'status-cancelled',
            self::Transferred => 'status-transferred',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
