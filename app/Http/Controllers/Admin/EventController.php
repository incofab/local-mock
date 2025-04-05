<?php

namespace App\Http\Controllers\Admin;

use App\Actions\EndExam;
use App\Actions\EventExamsHandler;
use App\Actions\ExtendExamTime;
use App\Actions\SyncEvents;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
  function index()
  {
    $query = Event::query()->withCount('exams')->latest();
    return view('admin.events.index', [
      'records' => paginateFromRequest($query),
    ]);
  }

  function show(Event $event)
  {
    // dd(json_encode($event->toArray(), JSON_PRETTY_PRINT));
    return view('admin.events.show', [
      'event' => $event,
    ]);
  }

  function syncEvents()
  {
    SyncEvents::make()->all();
    return back()->with('message', 'Events synced successfully');
  }

  function refreshEvent(Event $event)
  {
    SyncEvents::make()->single($event);
    return back()->with('message', 'Events refreshed successfully');
  }

  function evaluateEVent(Event $event)
  {
    EndExam::make()->endEventExams($event);
    return back()->with('message', 'Result evaluated successfully');
  }

  /**
   * Download and redownload event details
   */
  function download(Event $event)
  {
    $res = (new EventExamsHandler($event))->downloadEventContent();
    return back()->with(
      $res->isSuccessful() ? 'message' : 'error',
      $res->getMessage()
    );
  }

  function uploadEventExams(Event $event)
  {
    $res = (new EventExamsHandler($event))->uploadEventExams();
    return back()->with('message', $res->getMessage());
  }

  function extentTimeView(Event $event)
  {
    return view('admin.events.extend-time', ['event' => $event]);
  }

  function extentTimeStore(Event $event, Request $request)
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
}
