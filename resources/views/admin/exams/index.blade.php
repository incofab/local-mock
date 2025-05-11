@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Exams'])
@section('content')
<div>
    <div class="clearfix mb-2">
        <h5 class="float-left">Exams for {{$event->title}}</h5>
        <div class="float-end">
            <a href="{{route('admin.events.extend-time', $event)}}" class="btn btn-primary">
                <i class="fa fa-clock"></i> Extend Time
            </a>
            <a href="{{route('admin.events.evaluate', $event)}}" class="btn btn-success">
                <i class="fa fa-reload"></i> Evaluate Results
            </a>
        </div>
    </div>
    @include('common.message')
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="examTable">
            <thead>
                <tr>
                    <th>Exam No</th>
                    <th>Student</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>Subjects</th>
                    <th>End Time</th>
                    <th>Questions</th>
                    <th>Uploaded</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                <?php
                $examFileData = $record->isActive()
                  ? \App\Helpers\ExamHandler::make()->getExamFileData(
                    $record->exam_no
                  )
                  : null;
                $isOngoing = $record->isOngoing($examFileData);
                ?>
                <tr data-end_time="{{$isOngoing ? $record->end_time : ''}}">
                    <td>{{$record->exam_no}} <a href="{{route('admin.exams.exam-no.edit', [$record])}}"><i class="fa fa-pencil"></i></a></td>
                    <td>{{$record->student->name}}</td>
                    <td>{{$record->status}}</td>
                    <td>{{$record->score}}</td>
                    <td><small>{{$record->exam_courses->map(fn($item) => "{$item->course_code}")->join(', ')}}</small></td>
                    <td class="end-time"></td>
                    <td>{{$record->num_of_questions}}</td>
                    <td class="text-center">
                        @if ($record->uploaded_at)
                            <div><i class="fa fa-check text-success"></i></div>
                        @endif
                        <div>
                            <i class="fa fa-times text-danger"></i> 
                            <small>{{$record->upload_message}}</small>
                        </div>
                    </td>
                    <td><small>{{$record->created_at}}</small></td>
                    <td>
                        @if($record->canExtendTime())
                        <a href="{{route('admin.exams.extend-time', $record)}}" class="btn btn-primary btn-sm mt-2">
                            <i class="fa fa-clock"></i> Extend Time
                        </a>
                        @endif
                        @if($isOngoing || $record->canExtendTime())
                        <a href="{{route('admin.exams.evaluate', $record)}}" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Do you want to submit this exam?')">
                            <i class="fa fa-reload"></i> {{($record->score == null) ? 'Evaluate' : 'Re-Evaluate'}}
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function updateCountdown() {
    $('#examTable tbody tr').each(function() {
    const endTimeStr = $(this).attr('data-end_time');
    
    if(!endTimeStr){
        return;
    }
    const endTime = new Date(endTimeStr); // Convert to Date object
    const currentTime = new Date(); // Get current time
    // console.log(endTime, currentTime);
    
    let diff = endTime - currentTime;

    // If the exam has already started, set the time to "00:00:00"
    if (diff <= 0) {
        $(this).find('.end-time').text('');
    } else {
        const hours = Math.floor(diff / (1000 * 60 * 60));
        diff %= (1000 * 60 * 60);
        const minutes = Math.floor(diff / (1000 * 60));
        diff %= (1000 * 60);
        const seconds = Math.floor(diff / 1000);

        // Format as HH:MM:SS
        const timeDiffStr = 
        String(hours).padStart(2, '0') + ':' +
        String(minutes).padStart(2, '0') + ':' +
        String(seconds).padStart(2, '0');

        $(this).find('.end-time').text(timeDiffStr);
    }
    });
}
updateCountdown();
setInterval(updateCountdown, 5 * 1000);
</script>
@endsection
