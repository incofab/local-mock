<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSessionFactory extends Factory
{
  public function definition()
  {
    $sessions = range(2001, 2025); //['2001', '2002', '2003', '2004', '2005', '2006'];

    return [
      'course_id' => Course::factory(),
      'category' => '',
      'session' => fake()->randomElement($sessions),
    ];
  }

  public function questions($count = 10)
  {
    if ($count < 1) {
      return $this->state(fn($attr) => []);
    }

    return $this->afterCreating(function (CourseSession $model) use ($count) {
      Question::factory($count)->courseSession($model)->make();
    });
  }

  public function theoryQuestions($count = 2)
  {
    if ($count < 1) {
      return $this->state(fn($attr) => []);
    }

    return $this->state(
      fn($attr) => [
        'theory_questions' => collect(range(1, $count))
          ->map(
            fn($questionNo) => [
              'course_session_id' => null,
              'question_no' => $questionNo,
              'question_sub_number' => null,
              'question' => fake()->paragraph,
              'marks' => fake()->randomFloat(1, 1, 20),
              'answer' => fake()->paragraph,
              'marking_scheme' => fake()->paragraph,
            ]
          )
          ->toArray(),
      ]
    );
  }

  public function course(Course $course)
  {
    return $this->state(fn($attr) => ['course_id' => $course]);
  }
}
