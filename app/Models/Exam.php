<?php

namespace App\Models;

use App\Enums\ExamStatus;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $event_id
 * @property int $student_id
 * @property int $num_of_questions
 * @property int $score
 * @property string $exam_no
 * @property array $attempts
 * @property Event $event
 * @property Student $student
 * @property Collection<int, \App\Models\ExamCourse> $exam_courses
 */
class Exam extends Model
{
  use HasFactory;
  protected $casts = [
    'event_id' => 'integer',
    'num_of_questions' => 'integer',
    'status' => ExamStatus::class,
    'attempts' => AsArrayObject::class,
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'pause_time' => 'datetime',
  ];
  protected $fillable = [
    'id',
    'event_id',
    'student_id',
    'exam_no',
    'time_remaining',
    'start_time',
    'pause_time',
    'end_time',
    'score',
    'num_of_questions',
    'status',
    'meta',
    'attempts',
    'student',
    'exam_courses',
  ];

  function examCourses(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return collect($valueArr)->map(function ($item) {
          $examCourse = new ExamCourse($item);
          $courseSession = new CourseSession($item['course_session'] ?? []);
          $courseSession->course = new Course(
            $item['course_session']['course'] ?? []
          );
          $examCourse->course_session = $courseSession;
          return $examCourse;
        });
      },
      set: fn($value) => json_encode($value)
    );
  }
  function isActive()
  {
    return $this->status === ExamStatus::Active;
  }
  function isEnded()
  {
    return $this->status === ExamStatus::Ended;
  }
  function isOngoing($examFileData)
  {
    $isEnded = ($examFileData['status'] ?? null) === ExamStatus::Ended->value;
    // info([$this->exam_no, $isEnded]);
    return !$isEnded && $this->status === ExamStatus::Active;
  }
  function canExtendTime()
  {
    return $this->status === ExamStatus::Active ||
      $this->status === ExamStatus::Ended;
  }

  function markAsStarted()
  {
    $this->fill([
      'start_time' => now(),
      'status' => ExamStatus::Active,
      'pause_time' => null,
      'end_time' => now()->addMinutes($this->event->duration),
    ])->save();
  }

  function markAsEnded($totalScore, $totalNumOfQuestions, $attempts = [])
  {
    $this->fill([
      'status' => ExamStatus::Ended,
      'score' => $totalScore,
      'num_of_questions' => $totalNumOfQuestions,
      'attempts' => $attempts,
    ])->save();
  }

  function markAsPaused()
  {
    $this->fill([
      'status' => ExamStatus::Paused,
      'pause_time' => now(),
      'start_time' => null,
      'end_time' => null,
    ])->save();
  }

  function student(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        return new Student(json_decode($value, true) ?? []);
      },
      set: fn($value) => json_encode($value)
    );
  }

  /** @return int the remaining time in seconds */
  function getTimeRemaining()
  {
    $timeRemaining = now()->diffInSeconds($this->end_time);
    return $timeRemaining < 1 ? 0 : $timeRemaining;
  }

  function event()
  {
    return $this->belongsTo(Event::class);
  }
}
