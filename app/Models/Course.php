<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $course_code
 * @property string $course_title
 * @property Collection<int, \App\Models\CourseSession> $courseSessions
 */
class Course extends Model
{
  use HasFactory;
  protected $guarded = [];
  function courseSessions()
  {
    return $this->hasMany(CourseSession::class);
  }
}
