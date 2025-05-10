<?php
namespace App\Actions;

use App\Models\CourseSession;
use App\Models\Event;
use App\Models\Exam;
use App\Support\ContentFilePath;
use App\Support\WebsiteHelper;
use Illuminate\Support\Facades\File;
use ZipArchive;

class EventExamsHandler
{
  private ContentFIlePath $filePath;
  function __construct(private Event $event)
  {
    $this->filePath = new ContentFilePath($event->id);
  }

  function isDownloaded(): bool
  {
    return is_dir($this->filePath->getBaseFolder());
  }

  /**
   * Download event and its exam details from the server
   */
  function downloadEventContent()
  {
    // Extend the PHP timeout to 120 minutes (7200 seconds)
    ini_set('max_execution_time', 2 * 60 * 60);

    $event = WebsiteHelper::make()->getEventForExam($this->event->id);
    $exams = WebsiteHelper::make()->getExams($this->event->id);

    if (!$event) {
      return failRes('Event record not found');
    }
    if (empty($exams)) {
      return failRes('Exam record not found');
    }

    SyncEvents::make()->saveToFile($event, $exams);
    /*
    if (is_dir($this->filePath->getBaseFolder())) {
      File::deleteDirectory($this->filePath->getBaseFolder());
    }
    $this->filePath->createFolders();

    $eventCourses =
      $event['external_content_id'] ?? false
        ? $event['external_event_courses']
        : $event['event_courses'];

    foreach ($eventCourses as $eventCourse) {
      $courseSessionFilename = $this->filePath->courseSessionFilename(
        $eventCourse['course_session_id']
      );
      file_put_contents(
        $courseSessionFilename,
        json_encode($eventCourse['course_session'])
      );
    }

    foreach ($exams as $examData) {
      $examData['event_id'] = $this->event->id;
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
        $this->filePath->examFilename($exam->exam_no),
        json_encode($exam)
      );
    }

    (new CompileEventImages(
      new Event($event),
      $this->filePath->getImagesFolder()
    ))->run();
    */
    return successRes('Exams downloaded successfully');
  }

  function getCourseSession($courseSessionId): CourseSession|null
  {
    $content = @file_get_contents(
      $this->filePath->courseSessionFilename($courseSessionId)
    );
    $content = json_decode($content ?? '', true);
    return $content ? new CourseSession($content) : null;
  }
  /**
   * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question>|null $questions
   */
  function getQuestions($courseSessionId)
  {
    return $this->getCourseSession($courseSessionId)?->questions ?? [];
  }

  /** @deprecated */
  function listExams()
  {
    // return $this->event->exams()->with('examCourses')->get();
    $examNos = $this->event->exams()->get()->pluck('exam_no')->toArray();
    $examFiles = File::allFiles($this->filePath->getExamsFolder());
    $exams = [];
    foreach ($examFiles as $key => $examFile) {
      $filename = $examFile->getFilename();
      if (in_array($filename, $examNos)) {
        $examData = json_decode(
          file_get_contents($examFile->getPathname()),
          true
        );
        $exams[] = new Exam($examData);
      }
    }
    return $exams;
  }

  function getExam($examNo): Exam|null
  {
    $content = @file_get_contents($this->filePath->examFilename($examNo));
    $content = json_decode($content ?? '', true);
    return $content ? new Exam($content) : null;
  }

  function uploadEventExams()
  {
    $this->event
      ->exams()
      ->getQuery()
      ->chunk(100, function ($exams) {
        $success = WebsiteHelper::make()->uploadExams($exams->toArray());
      });
    $this->event->fill(['uploaded_at' => now()])->save();
    return successRes('Event exams uploaded');
  }

  /** Not in use at the moment */
  private function downloadAndUnzipEventContent(Event $event)
  {
    try {
      $zipUrl = ''; // WebsiteHelper::make()->eventContentUrl($event);
      $savePath = storage_path('app/public/sample.zip');

      $zipContents = @file_get_contents($zipUrl);
      if ($zipContents === false) {
        return failRes('Content not found');
      }

      file_put_contents($savePath, $zipContents);
      $extractTo = public_path("content/event_{$event->id}");

      $zip = new ZipArchive();
      if ($zip->open($savePath) === true) {
        $zip->extractTo($extractTo);
        $zip->close();
      } else {
        return failRes('Failed to extract ZIP file.');
      }

      return successRes('Event content downloaded successfully');
    } catch (\Exception $e) {
      return failRes('Error: ' . $e->getMessage());
    }
  }
}
