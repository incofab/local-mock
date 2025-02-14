<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $course_session_id
 * @property int $from
 * @property int $to
 * @property CourseSession $courseSession
 */
class Passage extends Model
{
  use HasFactory;
  protected $guarded = [];

  function courseSession()
  {
    return $this->belongsTo(CourseSession::class);
  }
}
