<?php
namespace App\Support\Platform;

class MockExamscholarsUrl extends PlatformUrl
{
  function getBaseUrl($code): string
  {
    return 'https://mock.examscholars.com/api/';
  }

  private function instRoute(string $route = ''): string
  {
    return $this->baseUrl . "institutions/{$this->code}/$route";
  }

  function listEvents(): string
  {
    return $this->instRoute('events');
  }

  public function showEvent(int $eventId): string
  {
    return $this->instRoute("events/{$eventId}/show");
    // return $this->baseUrl . "events/{$eventId}/show";
  }

  public function showDeepEvent(int $eventId): string
  {
    return $this->instRoute("events/{$eventId}/deep-show");
    // return $this->baseUrl . "events/{$eventId}/deep-show";
  }

  public function deepShowEventByCode(string $eventCode): string
  {
    return $this->instRoute("events/{$eventCode}/deep-show-by-code");
    // return $this->baseUrl . "events/{$eventCode}/deep-show-by-code";
  }

  // public function uploadEventResult(int $eventId): string
  // {
  //   return $this->baseUrl . "events/{$eventId}/upload-result";
  // }

  public function listEventExams(int $eventId): string
  {
    return $this->instRoute("events/{$eventId}/exams");
    // return $this->baseUrl . "events/{$eventId}/exams";
  }

  public function showInstitution(): string
  {
    return $this->instRoute('show-institution');
    // return $this->baseUrl . "institutions/{$this->code}/show-institution";
  }

  public function uploadExams(): string
  {
    return $this->instRoute('exams/upload');
    // return $this->baseUrl . 'exams/upload';
  }
}
