<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $url
 * @property string|null $company
 * @property string|null $salary
 * @property string|null $format_work
 * @property string|null $skills
 * @property array $top_questions
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $job_title
 * @property string|null $city
 * @property string|null $industry
 * @property string|null $benefits
 */
class Vacancy extends Model
{
    protected $table = 'vacancies';

    protected $fillable = [
        'url',
        'company',
        'salary',
        'format_work',
        'skills',
        'top_questions',
        'job_title',
        'city',
        'industry',
        'benefits',
    ];

    protected $casts = [
        'top_questions' => 'array',
    ];

    public function event()
    {
        return $this->hasOne(Event::class);
    }
}
