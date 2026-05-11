<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $exam_id
 * @property int $course_session_id
 * @property int $num_of_questions
 * @property int $score
 * @property int $theory_num_of_questions
 * @property float $theory_score
 * @property float $theory_max_score
 * @property ?array $theory_question_scores
 * @property bool $theory_evaluated
 * @property ?string $course_code
 * @property ?string $session
 * @property Exam $exam
 * @property CourseSession $course_session
 */
class ExamCourse extends Model
{
  use HasFactory;
  protected $guarded = [];
  protected $casts = [
    'course_session_id' => 'integer',
    'num_of_questions' => 'integer',
    'score' => 'float',
    'theory_num_of_questions' => 'integer',
    'theory_score' => 'float',
    'theory_max_score' => 'float',
    'theory_question_scores' => 'array',
    'theory_evaluated' => 'boolean',
  ];

  function courseSession(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return new CourseSession($valueArr);
      },
      set: fn($value) => json_encode($value)
    );
  }
}
