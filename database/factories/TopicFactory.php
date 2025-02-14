<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TopicFactory extends Factory
{
  function definition()
  {
    $couseIDs = \App\Models\Course::all('id')->pluck('id')->toArray();

    return [
      'course_id' => fake()->randomElement($couseIDs),
      'title' => fake()->words(8, true),
      'description' => fake()->paragraph,
    ];
  }
}
