<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $dateInterview
 * @property string $linkVacantion
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property ?int $vacancy_id
 * @property EventStatus $status
 * @property ?int parent_event_id
 */
class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'dateInterview',
        'linkVacantion',
        'comment',
        'vacancy_id',
        'status',
        'parent_event_id',
    ];

    protected $casts = [
        'dateInterview' => 'datetime',
        'status' => EventStatus::class,
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function childEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }
}
