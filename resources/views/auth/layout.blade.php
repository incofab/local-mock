<?php
$title = isset($title) ? $title : config('app.name'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{$title}}</title>

	<link rel="stylesheet" href="{{asset('lib/fontawesome-6.7/css/all.min.css')}}" >
	<link rel="stylesheet" href="{{asset('lib/bootstrap5.3/bootstrap.min.css')}}" >
     
    <script type="text/javascript" src="{{asset('lib/jquery3.7/jquery.min.js')}}"></script>    
</head>

<body class="bg-gradient-primary" style="height: 100vh; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: green;">
    <div class="container">
        @yield('content')
    </div>
	<!-- Bootstrap core JavaScript -->
	<script src="{{asset('lib/bootstrap5.3/bootstrap.bundle.min.js')}}"></script>
</body>

</html>