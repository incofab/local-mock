<?php

namespace Database\Factories;

use App\Models\CourseSession;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamCourseFactory extends Factory
{
    public function definition()
    {
        return [
            'exam_id' => Exam::factory(),
            'course_session_id' => CourseSession::factory(),
            'score' => $this->faker->numberBetween(0, 100),
            'num_of_questions' => $this->faker->numberBetween(10, 100),
            'theory_score' => 0,
            'theory_max_score' => 0,
            'theory_num_of_questions' => 0,
            'theory_question_scores' => null,
            'theory_evaluated' => false,
            'status' => 'active',
            'course_code' => 'English Dummy',
            'session' => fake()->randomElement(range(2000, 2025)),
        ];
    }

    public function exam(Exam $exam)
    {
        return $this->state(fn ($attr) => ['exam_id' => $exam]);
    }

    public function courseSession()
    {
        return $this->afterCreating(function (ExamCourse $model) {
            $institution = $model->exam->institution;
            CourseSession::factory()
                ->when($institution, fn ($q) => $q->institution($institution))
                ->create();
        });
    }

    public function questions($count = 10)
    {
        return $this->afterCreating(function (ExamCourse $model) use ($count) {
            Question::factory($count)
                ->courseSession($model->courseSession)
                ->create();
        });
    }
}
