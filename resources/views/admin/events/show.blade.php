@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Event Details'])
@section('content')
<div>
    <div>
        <h2>{{$event->title}}</h2>
        <div>{{$event->description}}</div>
    </div>
    <br>
    <div>
        <h4>Subjects</h4>
        <div>
            @foreach ($event->event_courses as $event_course)
            <p>{{$event_course->course_session?->course?->course_code}} - {{$event_course->course_session?->session}}</p>
            @endforeach
        </div>
    </div>
</div>
@endsection
