<?php

namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Event;
use App\Models\Exam;
use App\Support\Res;

class EndExam
{
  private ExamHandler $examHandler;

  public function __construct()
  {
    $this->examHandler = new ExamHandler();
  }

  public static function make()
  {
    return new self();
  }

  public function endEventExams(Event $event)
  {
    $exams = $event->exams()->with('event')->get();
    foreach ($exams as $exam) {
      $this->endExam($exam);
    }
  }

  public function endExam(Exam $exam): Res
  {
    $examCourses = $exam->exam_courses;
    if ($exam->status === ExamStatus::Ended) {
      return failRes('Exam already submitted');
    }
    if ($exam->status !== ExamStatus::Active) {
      return failRes('Exam is not active');
    }

    $totalScore = 0;
    $totalNumOfQuestions = 0;
    $eventExamHandler = new EventExamsHandler($exam->event);
    $attempts =
      $this->examHandler->getContent($exam->exam_no)->getExamTrack()[
        'attempts'
      ] ?? [];

    /** @var \App\Models\ExamCourse $examCourse */
    foreach ($examCourses as $examCourse) {
      $courseSession = $eventExamHandler->getCourseSession(
        $examCourse->course_session_id
      );
      $questions = $courseSession?->questions ?? collect();

      $scoreDetail = $this->examHandler->calculateScoreFromAttempts(
        $questions,
        $attempts
      );

      $score = $scoreDetail->getScore();
      $numOfQuestions = $scoreDetail->getNumOfQuestions();
      $theoryQuestions = $courseSession?->theory_questions ?? collect();
      $theoryNumOfQuestions = $theoryQuestions->count();
      $theoryMaxScore = $theoryQuestions->sum('marks');
      $theoryScore =
        $theoryNumOfQuestions > 0 && $examCourse->theory_evaluated
          ? $examCourse->theory_score
          : 0;
      $examCourse->fill([
        'score' => $score,
        'num_of_questions' => $numOfQuestions,
        'theory_score' => $theoryScore,
        'theory_max_score' => $theoryMaxScore,
        'theory_num_of_questions' => $theoryNumOfQuestions,
        'theory_evaluated' =>
          $theoryNumOfQuestions === 0 ? true : $examCourse->theory_evaluated,
        'status' => ExamStatus::Ended->value,
      ]);
      $totalScore += $score;
      $totalNumOfQuestions += $numOfQuestions + $theoryNumOfQuestions;
    }
    $exam->markAsEnded($totalScore, $totalNumOfQuestions, $attempts, [
      'exam_courses' => $examCourses->toArray(),
    ]);
    $this->examHandler->syncExamFile($exam, false);

    return successRes('Exam ended');
  }
}
