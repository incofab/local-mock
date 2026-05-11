<?php

namespace App\Support;

class LocalApiFixture
{
    public static function defaultPath(): string
    {
        return public_path('sample-data/local-api-fixture.json');
    }

    public static function path(): string
    {
        return env('LOCAL_MOCK_API_FIXTURE', self::defaultPath());
    }

    public static function make(): ?self
    {
        $path = self::path();
        if (! is_file($path)) {
            return null;
        }

        $data = json_decode(file_get_contents($path), true);
        if (! is_array($data)) {
            return null;
        }

        return new self($data);
    }

    public function __construct(private array $data) {}

    public function matchesInstitution(string $code, string $platform): bool
    {
        $institution = $this->institution();

        return ($institution['code'] ?? null) === $code &&
          ($institution['platform'] ?? null) === $platform;
    }

    public function institution(): array
    {
        return $this->data['institution'] ?? [];
    }

    public function events(?int $latestEventId = null): array
    {
        $events = $this->data['events'] ?? [];
        if (! $latestEventId) {
            return $events;
        }

        return collect($events)
            ->filter(fn ($event) => intval($event['id'] ?? 0) > $latestEventId)
            ->values()
            ->all();
    }

    public function event(int $eventId): array
    {
        return collect($this->data['events'] ?? [])
            ->firstWhere('id', $eventId) ?? [];
    }

    public function deepEvent(int $eventId): array
    {
        return $this->data['deep_events'][$eventId] ??
          $this->data['deep_events'][(string) $eventId] ??
          [];
    }

    public function eventByCode(string $eventCode): array
    {
        $event = $this->data['deep_events_by_code'][$eventCode] ?? null;
        if ($event) {
            return $event;
        }

        return collect($this->data['deep_events'] ?? [])
            ->first(fn ($event) => ($event['code'] ?? null) === $eventCode) ?? [];
    }

    public function exams(int $eventId): array
    {
        return $this->data['event_exams'][$eventId] ??
          $this->data['event_exams'][(string) $eventId] ??
          [];
    }

    public function uploadExams(array $exams): array
    {
        return [
            collect($exams)
                ->map(
                    fn ($exam) => [
                        'exam_no' => $exam['exam_no'] ?? '',
                        'message' => 'Uploaded to local fixture',
                    ]
                )
                ->all(),
            [],
        ];
    }
}
