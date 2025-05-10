<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Models\Event;
use App\Models\Exam;
use App\Support\ContentFilePath;
use App\Support\WebsiteHelper;
use Illuminate\Support\Facades\File;

class SyncEvents
{
  private static $instance;
  static function make(): static
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function all()
  {
    $latestEvent = Event::latest()->first();
    $events = WebsiteHelper::make()->getEvents($latestEvent?->id);
    foreach ($events as $event) {
      Event::firstOrCreate(['id' => $event['id']], $event);
    }
  }

  function single(Event $event)
  {
    $eventData = WebsiteHelper::make()->getSingleEvent($event->id);
    if (empty($eventData)) {
      return failRes('Event record not found');
    }
    $event->fill(collect($eventData)->except('id')->toArray())->save();
  }

  function saveToFile($event, $exams = [])
  {
    $filePath = new ContentFilePath($event['id']);
    if (is_dir($filePath->getBaseFolder())) {
      File::deleteDirectory($filePath->getBaseFolder());
    }
    $filePath->createFolders();

    $eventCourses =
      $event['external_content_id'] ?? false
        ? $event['external_event_courses']
        : $event['event_courses'];

    foreach ($eventCourses as $eventCourse) {
      $courseSessionFilename = $filePath->courseSessionFilename(
        $eventCourse['course_session_id']
      );
      file_put_contents(
        $courseSessionFilename,
        json_encode($eventCourse['course_session'])
      );
    }

    foreach ($exams as $examData) {
      $examData['event_id'] = $event['id'];
      $exam = Exam::query()
        ->where([
          'exam_no' => $examData['exam_no'],
          'event_id' => $examData['event_id'],
        ])
        ->first();
      if ($exam) {
        if ($exam->status !== ExamStatus::Pending) {
          continue;
        }
        $exam->fill($examData)->save();
      } else {
        $exam = Exam::query()->create($examData);
      }

      file_put_contents(
        $filePath->examFilename($exam->exam_no),
        json_encode($exam)
      );
    }

    (new CompileEventImages(
      new Event($event),
      $filePath->getImagesFolder()
    ))->run();
  }
}
