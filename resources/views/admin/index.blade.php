@extends('admin.layout', ['pageTitle' => 'Admin Dashboard'])
@section('content')
<div>
    <h1>Welcome</h1>
    {{-- <p>This is a simple dashboard layout using Bootstrap 5. Customize it as needed!</p> --}}

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Events</h5>
                    <p class="card-text">See list of all your events </p>
                    <a href="{{route('admin.events.index')}}" class="btn btn-primary">View Events</a>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Card 2</h5>
                    <p class="card-text">Some quick example text to build on the card title.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Card 3</h5>
                    <p class="card-text">Some quick example text to build on the card title.</p>
                    <a href="#" class="btn btn-primary">Go somewhere</a>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection
