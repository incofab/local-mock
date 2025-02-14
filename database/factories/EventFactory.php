<?php
namespace Database\Factories;

use App\Models\EventCourse;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
  function definition()
  {
    return [
      'title' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'duration' => $this->faker->numberBetween(20, 120),
      'status' => 'active',
    ];
  }

  function active()
  {
    return $this->state(fn($attr) => ['status' => 'active']);
  }

  function eventCourses($count = 3, $questionCount = 10)
  {
    return $this->afterCreating(
      fn($event) => EventCourse::factory($count)
        ->event($event, $questionCount)
        ->create()
    );
  }

  function exams($count = 3)
  {
    return $this->afterCreating(
      fn($event) => Exam::factory($count)
        ->notStarted()
        ->event($event)
        ->examCourses(2)
        ->create()
    );
  }
}
