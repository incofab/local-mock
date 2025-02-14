<?php
namespace App\Actions;

use App\Models\Event;
use App\Support\WebsiteHelper;

class SyncEvents
{
  private static $instance;
  static function make(): static
  {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  function all()
  {
    $latestEvent = Event::latest()->first();
    $events = WebsiteHelper::make()->getEvents($latestEvent?->id);
    foreach ($events as $event) {
      Event::firstOrCreate(['id' => $event['id']], $event);
    }
  }

  function single(Event $event)
  {
    $eventData = WebsiteHelper::make()->getSingleEvent($event->id);
    if (empty($eventData)) {
      return failRes('Event record not found');
    }
    $event->fill(collect($eventData)->except('id')->toArray())->save();
  }
}
