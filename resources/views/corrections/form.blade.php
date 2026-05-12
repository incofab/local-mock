@extends('corrections.layouts.app')

@section('title', 'Find Exam Corrections')

@section('content')
  <section class="hero">
    <span class="eyebrow">Student access</span>
    <h1>Review your evaluated exam corrections.</h1>
    <p>
      Enter your exam number to view released corrections, correct answers,
      your selected answers, and theory guidance.
    </p>

    <form class="search-form" method="GET" action="{{route('corrections.index')}}">
      <input
        class="form-control form-control-lg"
        type="text"
        name="exam_no"
        value="{{old('exam_no', $examNo ?? '')}}"
        placeholder="Enter exam number"
        required
      >
      <button class="btn btn-primary" type="submit">
        <i class="fa fa-magnifying-glass"></i>
        View corrections
      </button>
    </form>

    @if($message)
      <div class="notice notice-warning">
        <i class="fa fa-circle-info"></i> {{$message}}
      </div>
    @endif
  </section>
@endsection
