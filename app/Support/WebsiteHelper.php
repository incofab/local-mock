<?php
namespace App\Support;

use App\Actions\InstitutionHandler;
use App\Models\Event;

class WebsiteHelper
{
  private $code;
  private $baseUrl;
  // private $institutionUrl;
  private static $instance;

  const LIST_EVENTS = 'events';
  const SHOW_EVENT = 'events/{event}/show';
  const SHOW_DEEP_EVENT = 'events/{event}/deep-show';
  const UPLOAD_EVENT_RESULT = 'events/{event}/upload-result';
  const LIST_EVENT_EXAMS = 'events/{event}/exams';
  const SHOW_INSTITUTION = 'show-institution';
  const UPLOAD_EXAMS = 'exams/upload';

  function __construct()
  {
    $this->baseUrl = config('services.mock-base-url') . 'api/';
    $this->code = InstitutionHandler::getInstance()->getInstitution()?->code;
    // $this->institutionUrl = $this->forCode($this->code);
  }

  public static function make(): static
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  static function showInstitutionUrl(string $code)
  {
    return config('services.mock-base-url') .
      'api/' .
      "institutions/{$code}/" .
      self::SHOW_INSTITUTION;
  }

  function url(string $url, array $params = [], $forInstitution = true)
  {
    $forInstitution = $forInstitution && $this->code;
    foreach ($params as $key => $value) {
      $url = str_ireplace("{{$key}}", $value, $url);
    }
    $url =
      config('services.mock-base-url') .
      'api/' .
      ($forInstitution ? "institutions/{$this->code}/" : '') .
      $url;
    return $url;
  }

  // function forCode($code): string
  // {
  //   return $this->baseUrl . "institutions/{$code}/";
  // }

  function getBaseUrl(): string
  {
    return $this->baseUrl;
  }

  function eventContentUrl(Event $event)
  {
    return config('services.mock-base-url') . "content/event_{$event->id}.zip";
  }

  function getEvents($latestEventId): array
  {
    $res = http()->get($this->url(self::LIST_EVENTS), [
      'latest_event_id' => $latestEventId,
    ]);
    return $res->json('data', []);
  }

  function getSingleEvent($eventId): array
  {
    $res = http()->get($this->url(self::SHOW_EVENT, ['event' => $eventId]));
    return $res->json('data');
  }

  /**
   * Get exams for a particular event
   * @param int $eventId
   * @return array{} $exams
   */
  function getExams($eventId): array
  {
    $res = http()->get(
      $this->url(self::LIST_EVENT_EXAMS, ['event' => $eventId])
    );
    return $res->json('data', []);
  }

  function uploadExams(array $exams): bool
  {
    $res = http()->post($this->url(self::UPLOAD_EXAMS), ['exams' => $exams]);
    return $res->ok();
  }

  /**
   * Get exams for a particular event
   * @param int $eventId
   * @return array {
   * id: int,
   * title: string,
   * description: string,
   * event_courses: array {
   *    course_session_id: int,
   *    course_session: array {
   *        id: int,
   *        session: string,
   *        course_id: int,
   *        course: {
   *          id: int,
   *          course_code: string,
   *          course_title: string,
   *        },
   *       questions: \App\Models\Question[],
   *   },
   *   exams: \App\Model\Exam[]
   *  }[]
   * }
   */
  function getEventForExam($eventId): array
  {
    $res = http()->get(
      $this->url(self::SHOW_DEEP_EVENT, ['event' => $eventId])
    );
    return $res->json('data', []);
  }
}
