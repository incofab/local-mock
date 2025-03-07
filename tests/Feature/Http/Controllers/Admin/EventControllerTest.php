<?php

use App\Actions\EndExam;
use App\Actions\EventExamsHandler;
use App\Actions\FakeData;
use App\Actions\SyncEvents;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
  $this->user = User::factory()->create();
  actingAs($this->user);
  FakeData::make()->run();
});

it('can display the index page', function () {
  $events = Event::factory()->count(3)->create();

  $response = get(route('admin.events.index'));

  $response->assertStatus(200);
  $response->assertViewIs('admin.events.index');
  $response->assertViewHas('records');
  $records = $response->viewData('records');
  expect($records->count())->toBe($events->count());
  foreach ($events as $event) {
    assertDatabaseHas('events', ['id' => $event->id]);
  }
});

it('can display the show page', function () {
  $event = Event::factory()->create();

  $response = get(route('admin.events.show', $event));

  $response->assertStatus(200);
  $response->assertViewIs('admin.events.show');
  $response->assertViewHas('event');
  expect($response->viewData('event')->id)->toBe($event->id);
  assertDatabaseHas('events', ['id' => $event->id]);
});

it('can sync events', function () {
  //   $syncEventsMock = mock(SyncEvents::class)->expect(all: fn() => null);
  //   $this->app->instance(SyncEvents::class, $syncEventsMock);

  $response = get(route('admin.events.sync'));

  $response->assertStatus(302);
  $response->assertRedirect();
  $response->assertSessionHas('message', 'Events synced successfully');
});

it('can refresh an event', function () {
  $event = Event::factory()->create();
  $response = get(route('admin.events.refresh', $event));

  $response->assertStatus(302);
  $response->assertRedirect();
  $response->assertSessionHas('message', 'Events refreshed successfully');
});

it('can evaluate an event', function () {
  $event = Event::factory()->create();

  $response = get(route('admin.events.evaluate', $event));

  $response->assertStatus(302);
  $response->assertRedirect();
  $response->assertSessionHas('message', 'Result evaluated successfully');
});

it('can download event content successfully', function () {
  $event = Event::factory()->create();
  $response = get(route('admin.events.download', $event));

  $response->assertStatus(302);
  $response->assertRedirect();
});

it('can download event content with failure', function () {
  $event = Event::factory()->create();

  $response = get(route('admin.events.download', $event));

  $response->assertStatus(302);
  $response->assertRedirect();
});

it('can upload event exams', function () {
  $event = Event::factory()->create();

  $response = get(route('admin.events.upload', $event));

  $response->assertStatus(302);
  $response->assertRedirect();
});
