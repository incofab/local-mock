<?php
namespace Database\Factories;

use App\Models\CourseSession;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventCourseFactory extends Factory
{
  function definition()
  {
    return [
      'event_id' => Event::factory(),
      'course_session_id' => CourseSession::factory(),
      'status' => 'active',
    ];
  }

  function event(Event $event, $questionCount = 0)
  {
    return $this->state(
      fn($attr) => [
        'event_id' => $event->id,
        ...$event->institution
          ? [
            'course_session_id' => CourseSession::factory()
              ->questions($questionCount)
              ->institution($event->institution),
          ]
          : [],
      ]
    );
  }
}
