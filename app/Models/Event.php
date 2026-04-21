<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $dateInterview
 * @property string $linkVacantion
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'dateInterview',
        'linkVacantion',
        'comment',
    ];

    protected $casts = [
        'dateInterview' => 'datetime',
    ];
}
