@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Events'])
@section('content')
<div>
    <div class="clearfix">
        <h2 class="float-start">Events</h2>
        <a href="{{route('admin.events.sync')}}" class="btn btn-primary float-end"
            onclick="return confirm('Sync your app with the ')">
            <i class="fa fa-reload"></i> Sync Events
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Subjects</th>
                    <th>Duration (mins)</th>
                    <th>Exams</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                <tr>
                    <td><a href="{{route('admin.events.show', $record)}}">{{$record->title}}</a></td>
                    <td>{{$record->getEventCourses()->count()}}</td>
                    <td>{{$record->duration}}</td>
                    <td>{{$record->exams_count}}</td>
                    <td>{{$record->created_at}}</td>
                    <td>
                        <a href="{{route('admin.exams.index', $record)}}" class="btn btn-success btn-sm btn-link mt-2">
                            Exams
                        </a>
                        <a href="{{route('admin.events.refresh', $record)}}" class="btn btn-success btn-sm mt-2">
                            <i class="fa fa-refresh"></i> Refresh
                        </a>
                        <a href="{{route('admin.events.download', $record)}}" class="btn btn-info btn-sm mt-2">
                            <i class="fa fa-download"></i> Download
                        </a>
                        <a href="{{route('admin.events.upload', $record)}}" class="btn btn-info btn-sm mt-2">
                            <i class="fa fa-upload"></i> Upload
                        </a>
                        <a href="{{route('admin.events.evaluate', $record)}}" class="btn btn-danger btn-sm mt-2" 
                            onclick="return confirm('Do you want to submit all exams in this event?')">
                            <i class="fa fa-check"></i> Calculate
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
