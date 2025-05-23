@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Extend Exam Time'])
@section('content')
<div>
    <div class="clearfix mb-2">
        <h5 class="float-left">Extend Time</h5>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <br>
                <br>
                <div class="card shadow">
                    {{-- <div class="card-header">Extend Time</div> --}}
                    <div class="card-body">
                        <div>
                            <h5 class="text-center">Extend Time for all Exams in this event</h5>
                            <p><b>Event: </b> <span>{{$event->title}}</span></p>
                            <p><b>Duration: </b> <span>{{$event->duration}}</span></p>
                            <br>
                            <div>
                                <form method="POST" action="" name="register" >
                                    @csrf
                                    <div class="form-group">
                                        <label for="">Duration (mins)</label>
                                        <input type="number" name="duration" value="{{old('duration')}}" class="form-control">
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary mx-auto" value="Submit">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
