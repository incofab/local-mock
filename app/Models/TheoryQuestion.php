<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $course_session_id
 * @property int $question_no
 * @property ?string $question_sub_number
 * @property string $question
 * @property float $marks
 * @property string $answer
 * @property ?string $marking_scheme
 */
class TheoryQuestion extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'course_session_id' => 'integer',
        'question_no' => 'integer',
        'marks' => 'float',
    ];
}
