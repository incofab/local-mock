<?php
namespace App\Support;

use App\Actions\InstitutionHandler;
use App\Support\Platform\PlatformUrl;

class WebsiteHelper
{
  private static $instance;

  private PlatformUrl $platformUrl;
  function __construct()
  {
    $institution = InstitutionHandler::getInstance()->getInstitution();
    $this->platformUrl = PlatformUrl::make(
      $institution->platform,
      $institution->code
    );
  }

  public static function make(): static
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function getEvents($latestEventId): array
  {
    $res = http()->get($this->platformUrl->listEvents(), [
      'latest_event_id' => $latestEventId,
    ]);

    return $res->json('data', []);
  }

  function getSingleEvent($eventId): array
  {
    $res = http()->get($this->platformUrl->showEvent($eventId));
    // dd([
    //   'data' => $res->json(),
    //   'url' => $this->platformUrl->showEvent($eventId),
    //   'eventId' => $eventId,
    // ]);
    return $res->json('data');
  }

  /**
   * Get exams for a particular event
   * @param int $eventId
   * @return array{} $exams
   */
  function getExams($eventId): array
  {
    $res = http()->get($this->platformUrl->listEventExams($eventId));

    // dd([
    //   'data' => $res->json(),
    //   'url' => $this->platformUrl->showEvent($eventId),
    //   'eventId' => $eventId,
    // ]);

    return $res->json('data', []);
  }

  function uploadExams(array $exams): bool
  {
    $res = http()->post($this->platformUrl->uploadExams(), ['exams' => $exams]);
    // info([
    //   'data' => json_encode($exams, JSON_PRETTY_PRINT),
    //   'res' => $res->json(),
    //   'url' => $this->url(self::UPLOAD_EXAMS),
    // ]);
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
    $res = http()->get($this->platformUrl->showDeepEvent($eventId));
    return $res->json('data', []);
  }

  function deepShowEventByCode($eventCode): array
  {
    $res = http()->get($this->platformUrl->deepShowEventByCode($eventCode));
    return $res->json('data', []);
  }
}
