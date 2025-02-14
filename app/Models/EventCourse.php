<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $event_id
 * @property int $course_session_id
 * @property \App\Models\event $event
 * @property CourseSession $course_session
 */
class EventCourse extends Model
{
  use HasFactory;
  protected $guarded = [];

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
