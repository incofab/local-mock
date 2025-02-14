<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property int $duration
 * @property Collection<int, \App\Models\EventCourse> $event_courses
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
  ];
  protected $casts = [
    // 'exam_nos' => AsArrayObject::class,
  ];

  function eventCourses(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return collect($valueArr)->map(function ($item) {
          return new EventCourse($item);
          // $eventCourse = new EventCourse($item);
          // $courseSession = new CourseSession($item['course_session'] ?? []);
          // $courseSession->course = new Course(
          //   $item['course_session']['course'] ?? []
          // );
          // $courseSession->questions = collect(
          //   $item['course_session']['questions'] ?? []
          // )->map(fn($question) => new Question($question));
          // $courseSession->passages = collect(
          //   $item['course_session']['passages'] ?? []
          // )->map(fn($content) => new Passage($content));
          // $courseSession->instructions = collect(
          //   $item['course_session']['instructions'] ?? []
          // )->map(fn($content) => new Instruction($content));
          // $eventCourse->course_session = $courseSession;
          // return $eventCourse;
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
