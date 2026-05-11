<?php

namespace App\Support;

use App\Actions\InstitutionHandler;
use App\Support\Platform\PlatformUrl;

class WebsiteHelper
{
  private PlatformUrl $platformUrl;

  private ?LocalApiFixture $localApiFixture;

  public function __construct()
  {
    $institution = InstitutionHandler::getInstance()->getInstitution();
    $this->platformUrl = PlatformUrl::make(
      $institution->platform,
      $institution->code
    );
    $fixture = LocalApiFixture::make();
    $this->localApiFixture = $fixture?->matchesInstitution(
      $institution->code,
      $institution->platform
    )
      ? $fixture
      : null;
  }

  public static function make(): static
  {
    return new self();
  }

  function getPlatformUrl()
  {
    return $this->platformUrl;
  }

  public function getEvents($latestEventId): array
  {
    if ($this->localApiFixture) {
      return $this->localApiFixture->events($latestEventId);
    }

    $res = http()->get($this->platformUrl->listEvents(), [
      'latest_event_id' => $latestEventId,
    ]);

    return $res->json('data', []);
  }

  public function getSingleEvent($eventId): array
  {
    if ($this->localApiFixture) {
      return $this->localApiFixture->event($eventId);
    }

    $res = http()->get($this->platformUrl->showEvent($eventId));

    // dd([
    //   'data' => $res->json(),
    //   'url' => $this->platformUrl->showEvent($eventId),
    //   'eventId' => $eventId,
    // ]);
    return $res->json('data', []);
  }

  /**
   * Get exams for a particular event
   *
   * @param  int  $eventId
   * @return array{} $exams
   */
  public function getExams($eventId): array
  {
    if ($this->localApiFixture) {
      return $this->localApiFixture->exams($eventId);
    }

    $res = http()->get($this->platformUrl->listEventExams($eventId));

    // dd([
    //   'data' => $res->json(),
    //   'url' => $this->platformUrl->showEvent($eventId),
    //   'eventId' => $eventId,
    // ]);

    return $res->json('data', []);
  }

  public function uploadExams(array $exams): array
  {
    if ($this->localApiFixture) {
      return $this->localApiFixture->uploadExams($exams);
    }

    $res = http()->post($this->platformUrl->uploadExams(), ['exams' => $exams]);
    // info([
    //   'data' => json_encode($exams, JSON_PRETTY_PRINT),
    //   'res' => $res->json(),
    //   'url' => $this->url(self::UPLOAD_EXAMS),
    // ]);
    $success = $res->json('data.uploaded', []);
    $failed = $res->json('data.failed_uploads', []);

    return [$success, $failed];
  }

  /**
   * Get exams for a particular event
   *
   * @param  int  $eventId
   * @return array {
   *               id: int,
   *               title: string,
   *               description: string,
   *               event_courses: array {
   *               course_session_id: int,
   *               course_session: array {
   *               id: int,
   *               session: string,
   *               course_id: int,
   *               course: {
   *               id: int,
   *               course_code: string,
   *               course_title: string,
   *               },
   *               questions: \App\Models\Question[],
   *               },
   *               exams: \App\Model\Exam[]
   *               }[]
   *               }
   */
  public function getEventForExam($eventId): array
  {
    if ($this->localApiFixture) {
      return $this->localApiFixture->deepEvent($eventId);
    }

    $res = http()->get($this->platformUrl->showDeepEvent($eventId));

    return $res->json('data', []);
  }

  public function deepShowEventByCode($eventCode): array
  {
    if ($this->localApiFixture) {
      return $this->localApiFixture->eventByCode($eventCode);
    }

    $res = http()->get($this->platformUrl->deepShowEventByCode($eventCode));

    return $res->json('data', []);
  }
}
