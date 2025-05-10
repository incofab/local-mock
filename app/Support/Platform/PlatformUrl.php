<?php
namespace App\Support\Platform;

use App\Enums\HostPlatform;
use App\Models\Institution;

abstract class PlatformUrl
{
  protected string $baseUrl;
  function __construct(protected string $code)
  {
    $this->baseUrl = $this->getBaseUrl($code);
  }

  abstract public function getBaseUrl($code): string;

  abstract public function listEvents(): string;

  abstract public function showEvent(int $eventId): string;

  abstract public function showDeepEvent(int $eventId): string;

  abstract public function deepShowEventByCode(string $eventCode): string;

  // abstract public function uploadEventResult(int $eventId): string;

  abstract public function listEventExams(int $eventId): string;

  abstract public function showInstitution(): string;

  abstract public function uploadExams(): string;

  static function makeFromInstitution(Institution $institution): static
  {
    return self::make($institution->platform, $institution->code);
  }

  static function make($platform, $code): static
  {
    return match ($platform) {
      HostPlatform::Edumanager->value => new EdumanagerUrl($code),

      HostPlatform::ExamscholarsMock->value => new MockExamscholarsUrl($code),

      default => new MockExamscholarsUrl($platform),
    };
  }
}
