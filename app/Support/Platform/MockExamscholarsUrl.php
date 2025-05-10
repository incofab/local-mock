<?php
namespace App\Support\Platform;

class MockExamscholarsUrl extends PlatformUrl
{
  function getBaseUrl($code): string
  {
    return 'https://mock.examscholars.com/api/v1/';
  }

  function listEvents(): string
  {
    return $this->baseUrl . 'events';
  }

  public function showEvent(int $eventId): string
  {
    return $this->baseUrl . "events/{$eventId}/show";
  }

  public function showDeepEvent(int $eventId): string
  {
    return $this->baseUrl . "events/{$eventId}/deep-show";
  }

  public function deepShowEventByCode(string $eventCode): string
  {
    return $this->baseUrl . "events/{$eventCode}/deep-show-by-code";
  }

  // public function uploadEventResult(int $eventId): string
  // {
  //   return $this->baseUrl . "events/{$eventId}/upload-result";
  // }

  public function listEventExams(int $eventId): string
  {
    return $this->baseUrl . "events/{$eventId}/exams";
  }

  public function showInstitution(): string
  {
    return $this->baseUrl . "institutions/{$this->code}/show-institution";
  }

  public function uploadExams(): string
  {
    return $this->baseUrl . 'exams/upload';
  }
}
