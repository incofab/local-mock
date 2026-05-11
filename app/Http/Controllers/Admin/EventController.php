<?php

namespace App\Http\Controllers\Admin;

use App\Actions\EndExam;
use App\Actions\EventExamsHandler;
use App\Actions\ExtendExamTime;
use App\Actions\SyncEvents;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Support\WebsiteHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
  public function index()
  {
    $query = Event::query()->withCount('exams')->latest('id');

    return view('admin.events.index', [
      'records' => paginateFromRequest($query),
    ]);
  }

  public function show(Event $event)
  {
    //dd(json_encode($event->toArray(), JSON_PRETTY_PRINT));
    return view('admin.events.show', [
      'event' => $event,
    ]);
  }

  public function syncEvents()
  {
    SyncEvents::make()->all();

    return back()->with('message', 'Events synced successfully');
  }

  public function refreshEvent(Event $event)
  {
    SyncEvents::make()->single($event);

    return back()->with('message', 'Events refreshed successfully');
  }

  public function evaluateEVent(Event $event)
  {
    EndExam::make()->endEventExams($event);

    return back()->with('message', 'Result evaluated successfully');
  }

  /**
   * Download and redownload event details
   */
  public function download(Event $event)
  {
    $res = (new EventExamsHandler($event))->downloadEventContent();

    return back()->with(
      $res->isSuccessful() ? 'message' : 'error',
      $res->getMessage()
    );
  }

  public function uploadEventExams(Event $event)
  {
    $res = (new EventExamsHandler($event))->uploadEventExams();

    return back()->with('message', $res->getMessage());
  }

  public function extentTimeView(Event $event)
  {
    return view('admin.events.extend-time', ['event' => $event]);
  }

  public function extentTimeStore(Event $event, Request $request)
  {
    $request->validate(['duration' => ['required', 'integer', 'min:1']]);

    $exams = $event->exams()->get();
    foreach ($exams as $key => $exam) {
      ExtendExamTime::make($exam)->run($request->duration);
    }

    return redirect(route('admin.exams.index', $exam->event))->with(
      'message',
      "All exams in this event have been extended by {$request->duration} mins"
    );
  }

  public function downloadByEventCode(Request $request)
  {
    if (!$request->isMethod('POST')) {
      return view('admin.events.enter-event-code');
    }
    $request->validate(['code' => ['required', 'string']]);

    $event = WebsiteHelper::make()->deepShowEventByCode($request->code);

    if (!$event) {
      return throw ValidationException::withMessages([
        'code' => 'Event record not found',
      ]);
    }

    $eventModel = Event::query()->updateOrCreate(
      ['id' => $event['id']],
      collect($event)->except('id')->toArray()
    );

    SyncEvents::make()->saveToFile($event, []);

    return redirect(route('admin.events.show', $eventModel))->with(
      'message',
      'Event downloaded successfully'
    );
  }
}
