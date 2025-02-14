<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $course_session_id
 * @property int $question_no
 * @property string $option_a
 * @property string $option_b
 * @property string $option_c
 * @property string $option_d
 * @property string $option_e
 * @property string $answer
 * @property CourseSession $courseSession
 */
class Question extends Model
{
  use HasFactory;
  protected $guarded = [];
}
