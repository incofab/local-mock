<?php

namespace App\Http\Controllers;

use App\Actions\EventExamsHandler;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ExamCorrectionController extends Controller
{
  public function index(Request $request)
  {
    $examNo = trim($request->query('exam_no', ''));

    if ($examNo === '') {
      return $this->formView('', null);
    }

    $exam = Exam::query()->where('exam_no', $examNo)->with('event')->first();

    if (!$exam) {
      return $this->formView($examNo, 'Exam record not found.');
    }

    if (!$exam->event?->show_corrections) {
      return $this->formView(
        $examNo,
        'Corrections are not available for this exam yet.'
      );
    }

    if (!$this->examHasBeenEvaluated($exam)) {
      return $this->formView(
        $examNo,
        'This exam result has not been fully evaluated yet.'
      );
    }

    return view('corrections.show', [
      'examNo' => $examNo,
      'exam' => $exam,
      'courses' => $this->coursesFor($exam),
    ]);
  }

  private function formView(string $examNo, ?string $message)
  {
    return view('corrections.form', [
      'examNo' => $examNo,
      'message' => $message,
    ]);
  }

  private function examHasBeenEvaluated(Exam $exam): bool
  {
    if (!$exam->isEnded()) {
      return false;
    }
    return true;
  }

  private function coursesFor(Exam $exam): Collection
  {
    $attempts = $exam->attempts?->getArrayCopy() ?? [];
    $eventExamHandler = new EventExamsHandler($exam->event);

    return $exam->exam_courses
      ->map(function ($examCourse) use ($attempts, $eventExamHandler) {
        $courseSession = $eventExamHandler->getCourseSession(
          $examCourse->course_session_id
        );

        $course = $courseSession?->course;
        $courseCode = $examCourse->course_code ?: $course?->course_code ?? '';
        $courseTitle = $course?->course_title ?? '';
        $title = trim($courseCode . ($courseTitle ? " - {$courseTitle}" : ''));

        return [
          'id' => $examCourse->course_session_id,
          'title' => $title ?: 'Course ' . $examCourse->course_session_id,
          'summary' => $this->courseSummary($examCourse),
          'obj_questions' => $this->objQuestionsFor(
            $courseSession?->questions ?? collect(),
            $attempts
          ),
          'theory_questions' => $this->theoryQuestionsFor(
            $courseSession?->theory_questions ?? collect(),
            $attempts,
            $examCourse->theory_question_scores ?? []
          ),
          'theory_evaluated' => $examCourse->theory_evaluated,
        ];
      })
      ->values();
  }

  private function courseSummary($examCourse): string
  {
    $parts = ["OBJ {$examCourse->score}/{$examCourse->num_of_questions}"];

    if ($examCourse->theory_num_of_questions > 0) {
      $parts[] = "Theory {$examCourse->theory_score}/{$examCourse->theory_max_score}";
    }

    return implode(' | ', $parts);
  }

  private function objQuestionsFor($questions, array $attempts): Collection
  {
    return $questions
      ->sortBy('question_no')
      ->values()
      ->map(function ($question) use ($attempts) {
        $studentAnswer = $attempts[$question->id] ?? null;

        return [
          'id' => $question->id,
          'number' => $question->question_no,
          'question' => $question->question,
          'answer' => $question->answer,
          'answer_meta' => $question->answer_meta ?? null,
          'student_answer' => $studentAnswer,
          'is_correct' =>
            $studentAnswer !== null && $studentAnswer === $question->answer,
          'options' => $this->optionsFor($question),
        ];
      });
  }

  private function optionsFor($question): array
  {
    return collect(['A', 'B', 'C', 'D', 'E'])
      ->map(function ($letter) use ($question) {
        $value = $question->{'option_' . strtolower($letter)} ?? null;

        if ($value === null || $value === '') {
          return null;
        }

        return [
          'letter' => $letter,
          'value' => $value,
        ];
      })
      ->filter()
      ->values()
      ->toArray();
  }

  private function theoryQuestionsFor(
    $questions,
    array $attempts,
    ?array $scores
  ): Collection {
    return $questions
      ->sortBy('question_no')
      ->values()
      ->map(function ($question) use ($attempts, $scores) {
        return [
          'id' => $question->id,
          'number' => trim(
            $question->question_no . ($question->question_sub_number ?: '')
          ),
          'question' => $question->question,
          'marks' => $question->marks,
          'answer' => $question->answer,
          'marking_scheme' => $question->marking_scheme,
          'student_answer' => $this->theoryAttempt($attempts, $question->id),
          'score' => $this->theoryScore($scores ?? [], $question->id),
        ];
      });
  }

  private function theoryAttempt(array $attempts, $questionId): ?string
  {
    return $attempts["theory-{$questionId}"] ?? null;
  }

  private function theoryScore(array $scores, $questionId): mixed
  {
    return $scores[$questionId] ?? ($scores["theory-{$questionId}"] ?? null);
  }
}
