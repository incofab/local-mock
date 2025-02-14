<?php
namespace Database\Factories;

use App\Enums\ExamStatus;
use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
  function definition()
  {
    return [
      'event_id' => Event::factory(),
      'student' => Student::factory()->make()->toArray(),
      'exam_no' => $this->faker->unique()->randomNumber(5, true),
      'time_remaining' => $this->faker->randomFloat(2, 0, 120),
      'start_time' => $this->faker->dateTime,
      'pause_time' => $this->faker->dateTime,
      'end_time' => $this->faker->dateTime,
      'score' => $this->faker->numberBetween(0, 100),
      'num_of_questions' => $this->faker->numberBetween(10, 100),
      'status' => ExamStatus::Active,
    ];
  }

  function notStarted()
  {
    return $this->state(
      fn(array $attr) => [
        'start_time' => null,
        'pause_time' => null,
        'end_time' => null,
      ]
    );
  }
  function ended()
  {
    return $this->state(
      fn(array $attr) => [
        'start_time' => now()->subHours(2),
        'pause_time' => null,
        'end_time' => now()->subMinutes(30),
        'active' => ExamStatus::Ended,
      ]
    );
  }

  function event(Event $event)
  {
    return $this->state(
      fn(array $attr) => [
        'event_id' => $event,
      ]
    );
  }

  function examCourses($count = 3)
  {
    return $this->afterCreating(
      fn(Exam $exam) => ExamCourse::factory($count)
        ->exam($exam)
        ->courseSession()
        ->create()
    );
  }
}
