@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Edit Exam No'])
@section('content')
<div>
    <div class="clearfix mb-2">
        <h5 class="float-left">Edit Exam Number</h5>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <br>
                <div class="alert alert-danger">
                    Only use this if the student entered the wrong Exam Number while starting the exam
                </div>
                <br>
                <div class="card shadow">
                    <div class="card-body">
                        <div>
                            <div>
                                <form method="POST" action="{{route('admin.exams.exam-no.update', $exam)}}" >
                                    @csrf
                                    <div class="form-group">
                                        <label for="">Exam No</label>
                                        <input type="text" name="exam_no" value="{{old('exam_no', $exam->exam_no)}}" class="form-control">
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
