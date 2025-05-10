<?php

namespace App\Http\Controllers\API;

use App\Actions\StartExam;
use App\Enums\ExamStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exam;
use App\Support\ContentFilePath;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
  /**
   * Starts or resumes an exam
   */
  function startExam(Request $request)
  {
    $request->validate([
      'exam_no' => ['required', 'string'],
      'student_code' => ['nullable', 'string'],
    ]);
    $exam = Exam::query()
      ->where('exam_no', $request->exam_no)
      ->with('event')
      ->first();
    if (!$exam) {
      return throw ValidationException::withMessages([
        'exam_no' => 'Exam record not found',
      ]);
    }
    return $this->examView($exam);
  }

  private function examView(Exam $exam)
  {
    $res = StartExam::make($exam)->getExamStartupData();
    if ($res->isNotSuccessful()) {
      return $this->fail([], $res->getMessage());
    }

    $exam = $res->exam;
    return $this->ok([
      'exam_track' => $res->exam_track,
      'exam' => $exam,
      'timeRemaining' => $exam->getTimeRemaining(),
      'baseUrl' => url('/'),
    ]);
  }

  function createExamByCode(Request $request)
  {
    $request->validate([
      'event_code' => ['required', 'string', 'exists:events,code'],
      'student_code' => ['nullable', 'string', 'confirmed'],
    ]);
    $event = Event::query()
      ->where('code', $request->event_code)
      ->firstOrFail();
    $exam = $this->createExam($event, $request->student_code);
    info($exam->toArray());
    // return $this->examView($exam);
    return $this->ok([
      'exam' => $exam,
      'exam_no' => $exam->exam_no,
    ]);
  }

  private function createExam(Event $event, $studentCode): Exam
  {
    $examNo = "{$event->code}-{$studentCode}";
    $exam = Exam::query()->where('exam_no', $examNo)->with('event')->first();
    if ($exam) {
      return $exam;
    }
    $exam = Exam::query()->create([
      'event_id' => $event->id,
      'student_id' => $studentCode,
      'exam_no' => $examNo,
      'time_remaining' => 0,
      'status' => ExamStatus::Pending,
    ]);
    $exam
      ->fill([
        'exam_courses' => $event->getEventCourses()->map(
          fn($eventCourse) => [
            'course_session_id' => $eventCourse->course_session_id,
            'num_of_questions' => $eventCourse->num_of_questions,
            'exam_id' => $exam->id,
          ]
        ),
      ])
      ->save();

    $filePath = new ContentFilePath($event->id);
    $filePath->createFolders();
    file_put_contents(
      $filePath->examFilename($exam->exam_no),
      json_encode($exam)
    );
    return $exam;
  }
}
