<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EventStage;
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
 * @property ?int $parent_event_id
 * @property EventStage $stage
 */
class Event extends Model
{
    protected $table = 'events';
    protected $appends = ['stage_label', 'stage_color'];

    protected $fillable = [
        'dateInterview',
        'linkVacantion',
        'comment',
        'vacancy_id',
        'status',
        'parent_event_id',
        'stage',
    ];

    protected $casts = [
        'dateInterview' => 'datetime',
        'status' => EventStatus::class,
        'stage' => EventStage::class,
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

    public function ancestors()
    {
        $ancestors = collect();
        $current = $this;
        while ($current->parentEvent) {
            $ancestors->prepend($current->parentEvent);
            $current = $current->parentEvent;
        }

        return $ancestors;
    }

    public function descendants()
    {
        $descendants = collect();
        $stack = $this->childEvents;
        while ($stack->isNotEmpty()) {
            $current = $stack->shift();
            $descendants->push($current);
            $stack = $stack->concat($current->childEvents);
        }

        return $descendants;
    }

    //аксессор
    public function getStageLabelAttribute(): string
    {
        return $this->stage?->cutLabel() ?? '';
    }

    public function getStageColorAttribute(): string
    {
        return $this->stage?->color() ?? '';
    }
}
