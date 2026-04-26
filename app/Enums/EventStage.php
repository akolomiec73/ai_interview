<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Стадии События
 */
enum EventStage: string
{
    case FirstMeet = 'first_meet';
    case TechnicalMeet = 'technical_meet';
    case TestTask = 'test_task';
    case OfferWaiting = 'offer_waiting';
    case ExtraMeet = 'extra_meet';

    public function label(): string
    {
        return match ($this) {
            self::FirstMeet => 'Первичная встреча (HR)',
            self::TechnicalMeet => 'Техническое собеседование',
            self::TestTask => 'Тестовое задание',
            self::OfferWaiting => 'Ожидание оффера',
            self::ExtraMeet => 'Дополнительная встреча',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FirstMeet => 'stage-first-meet',
            self::TechnicalMeet => 'stage-technical-meet',
            self::TestTask => 'stage-test-task',
            self::OfferWaiting => 'stage-offer-waiting',
            self::ExtraMeet => 'stage-extra-meet',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
