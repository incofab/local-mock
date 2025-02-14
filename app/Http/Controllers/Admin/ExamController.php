<?php

namespace App\Http\Controllers\Admin;

use App\Actions\EndExam;
use App\Actions\ExtendExamTime;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
  function index(Event $event)
  {
    $exams = $event->exams()->get();
    return view('admin.exams.index', ['event' => $event, 'records' => $exams]);
  }

  function evaluateExam(Exam $exam)
  {
    EndExam::make()->endExam($exam);
    return back()->with('message', 'Exam result evaluated successfully');
  }

  function extentTimeView(Exam $exam)
  {
    return view('admin.exams.extend-time', ['exam' => $exam]);
  }

  function extentTimeStore(Exam $exam, Request $request)
  {
    $request->validate(['duration' => ['required', 'integer', 'min:1']]);
    ExtendExamTime::make($exam)->run($request->duration);
    return redirect(route('admin.exams.index', $exam->event))->with(
      'message',
      "Exam time extended by {$request->duration} mins"
    );
  }
}
