@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Event Details'])
@section('content')
<div>
    <div>
        <h3>{{$event->title}}</h3>
        <div>{{$event->description}}</div>
    </div>
    <br>
    <div>
        <h4>Subjects</h4>
        <div>
            @foreach ($event->getEventCourses() as $eventCourse)
            <p>{{$eventCourse->course_session?->course?->course_code}} - {{$eventCourse->course_session?->session}}</p>
            @endforeach
        </div>
    </div>
</div>
@endsection
