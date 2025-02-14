<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Question;

class StartExam
{
  function __construct(private Exam $exam)
  {
  }

  static function make(Exam $exam)
  {
    return new self($exam);
  }

  function getExamStartupData($start = true)
  {
    if ($start && $this->canStartExam()) {
      $this->exam->markAsStarted();
    }

    if ($this->exam->status === ExamStatus::Ended) {
      return failRes('Exam has already ended');
    }

    $examHandler = new ExamHandler();
    $ret = $examHandler->syncExamFile($this->exam);
    if ($ret->isNotSuccessful()) {
      return failRes($ret->getMessage());
    }

    $ret = $examHandler->getContent($this->exam->exam_no);

    if (empty($ret->getExamTrack())) {
      $ret = failRes($ret->getMessage());
    }
    return successRes('', [
      'exam' => $this->prepareExam($this->exam),
      'exam_track' => $ret->getExamTrack(),
    ]);
  }

  private function prepareExam(Exam $exam)
  {
    $eventExamHandler = new EventExamsHandler($exam->event);
    /** @var ExamCourse $examCourse */
    foreach ($exam->exam_courses as $key => $examCourse) {
      $courseSession = $eventExamHandler->getCourseSession(
        $examCourse->course_session_id
      );
      $courseSession->questions = $courseSession->questions->map(function (
        Question $item
      ) {
        $item->answer = null;
        $item->answer_meta = null;
        return $item;
      });
      $examCourse->course_session = $courseSession;
    }
    $exam->event->event_courses = [];
    return $exam;
  }

  function canStartExam()
  {
    return in_array($this->exam->status, [
      ExamStatus::Pending,
      ExamStatus::Paused,
    ]);
    // return empty($this->exam->start_time) || !empty($this->exam->pause_time);
  }

  // function startExam()
  // {
  //   if (!$this->canStartExam()) {
  //     return;
  //   }
  //   $this->exam->markAsStarted();
  // }
}
