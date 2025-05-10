<?php
namespace App\Support\Platform;

class EdumanagerUrl extends PlatformUrl
{
  function getBaseUrl($code): string
  {
    return "https://edumanager.ng/api/offline-mock/institutions/{$code}/";
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
    return $this->baseUrl . 'show-institution';
  }

  public function uploadExams(): string
  {
    return $this->baseUrl . 'exams/upload';
  }
}
