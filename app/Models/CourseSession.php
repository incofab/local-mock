<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $course_id
 * @property string $session
 * @property string $general_instructions
 * @property ?\App\Models\Course $course
 * @property Collection<int, \App\Models\Question> $questions
 * @property Collection<int, \App\Models\TheoryQuestion> $theory_questions
 * @property Collection<int, \App\Models\Instruction> $instructions
 * @property Collection<int, \App\Models\Passage> $passages
 */
class CourseSession extends Model
{
  use HasFactory;

  protected $guarded = [];

  public function course(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        return new Course(json_decode($value ?? '[]', true) ?? []);
      },
      set: fn($value) => json_encode($value)
    );
  }

  public function questions(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value ?? '[]', true) ?? [];

        return collect($valueArr)->map(function ($item) {
          return new Question($item);
        });
      },
      set: fn($value) => json_encode($value)
    );
  }

  public function theoryQuestions(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value ?? '[]', true) ?? [];

        return collect($valueArr)->map(function ($item) {
          return new TheoryQuestion($item);
        });
      },
      set: fn($value) => json_encode($value)
    );
  }

  public function instructions(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value ?? '[]', true) ?? [];

        return collect($valueArr)->map(function ($item) {
          return new Institution($item);
        });
      },
      set: fn($value) => json_encode($value)
    );
  }

  public function passage(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value ?? '[]', true) ?? [];

        return collect($valueArr)->map(function ($item) {
          return new Passage($item);
        });
      },
      set: fn($value) => json_encode($value)
    );
  }
}
