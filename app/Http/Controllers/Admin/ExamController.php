<?php

namespace App\Http\Controllers\Admin;

use App\Actions\EndExam;
use App\Actions\ExtendExamTime;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

  function editExamNo(Exam $exam)
  {
    return view('admin.exams.edit-exam-no', ['exam' => $exam]);
  }

  function updateExamNo(Request $request, Exam $exam)
  {
    $data = $request->validate([
      'exam_no' => [
        'required',
        Rule::unique('exams', 'exam_no')->ignore($exam->id),
      ],
    ]);
    $exam->fill($data)->save();
    return redirect(route('admin.exams.index', $exam->event_id))->with(
      'message',
      'Exam number updated successfully'
    );
  }
}
