@extends('admin.layout', ['pageTitle' => 'Admin Dashboard | Extend Exam Time'])
@section('content')
<div>
    <div class="clearfix mb-2">
        <h5 class="float-left">Retrieve an Event Record</h5>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <br>
                <br>
                <div class="card shadow">
                    <div class="card-body">
                        <div>
                            <div>
                                <form method="POST" action="{{route('admin.events.download-by-code')}}" >
                                    @csrf
                                    <div class="form-group">
                                        <label for="">Enter Event Code</label>
                                        <input type="text" name="code" value="{{old('code')}}" class="form-control">
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
