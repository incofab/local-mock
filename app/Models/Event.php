<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property int $duration
 * @property int $external_content_id
 * @property Collection<int, \App\Models\EventCourse> $event_courses
 * @property Collection<int, \App\Models\EventCourse> $external_event_courses
 * @property Collection<int, \App\Models\Exam> $exams
 */
class Event extends Model
{
  use HasFactory;
  protected $fillable = [
    'id',
    'title',
    'description',
    'duration',
    'status',
    'event_courses',
    'external_content_id',
    'external_event_courses',
  ];
  protected $casts = [
    'uploaded_at' => 'datetime',
    'external_content_id' => 'integer',
  ];

  function eventCourses(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return collect($valueArr)->map(function ($item) {
          return new EventCourse($item);
        });
      },
      set: fn($value) => json_encode($value)
    );
  }

  function isExternal()
  {
    return $this->external_content_id;
  }

  function isNotExternal()
  {
    return !$this->external_content_id;
  }

  function getEventCourses()
  {
    if (!$this->isExternal()) {
      return $this->eventCourses;
    }
    return $this->external_event_courses;
  }
  function findCourseSession($courseSessionId): CourseSession|array|null
  {
    return $this->getEventCourses()
      ->filter(
        fn($item) => $item['course_session_id'] == intval($courseSessionId)
      )
      ->first()
      ?->getCourseSession();
  }

  function externalEventCourses(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return collect($valueArr)->map(function ($item) {
          $eventCourse = new EventCourse($item);
          $courseSession = new CourseSession($item['course_session'] ?? []);
          $courseSession['course'] = new Course(
            $item['course_session']['course'] ?? []
          );
          $eventCourse->course_session = $courseSession;
          return $eventCourse;
        });
      },
      set: fn($value) => json_encode($value)
    );
  }

  // function eventCourses()
  // {
  //   return $this->hasMany(EventCourse::class);
  // }

  function exams()
  {
    return $this->hasMany(Exam::class);
  }
}
