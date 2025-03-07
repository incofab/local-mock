<?php
namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class UserFactory extends Factory
{
  function definition()
  {
    return [
      'name' => fake()->name,
      'email' => fake()->unique()->safeEmail,
      'email_verified_at' => now(),
      'password' => Hash::make('password'), // password
      // '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
      'remember_token' => Str::random(10),
    ];
  }

  function admin()
  {
    return $this->state(
      fn(array $attr) => [
        'email' => config('app.admin.email'),
      ]
    );
  }
}
