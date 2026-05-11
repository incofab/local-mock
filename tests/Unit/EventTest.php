<?php

use App\Models\Event;
use App\Models\EventCourse;

it('returns an empty collection when event courses are missing', function () {
  $event = new Event();

  expect($event->eventCourses)->toHaveCount(0);
});

it('casts event courses from json into event course models', function () {
  $event = new Event();
  $event->setRawAttributes([
    'event_courses' => json_encode([
      [
        'course_session_id' => 15,
        'num_of_questions' => 40,
      ],
    ]),
  ]);

  $eventCourses = $event->getEventCourses();

  expect($eventCourses)->toHaveCount(1);
  expect($eventCourses->first())
    ->toBeInstanceOf(EventCourse::class)
    ->course_session_id->toBe(15);
});

it('returns external event courses when external content exists', function () {
  $event = new Event();
  $event->setRawAttributes([
    'external_content_id' => 40,
    'external_event_courses' => json_encode([
      [
        'course_session_id' => 30,
        'course_session' => [
          'course' => [
            'course_code' => 'MTH101',
          ],
        ],
      ],
    ]),
  ]);

  $eventCourses = $event->getEventCourses();

  expect($eventCourses)->toHaveCount(1);
  expect($eventCourses->first())
    ->toBeInstanceOf(EventCourse::class)
    ->course_session_id->toBe(30);
});
