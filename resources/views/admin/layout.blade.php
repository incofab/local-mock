<?php
$pageTitle = $pageTitle ?? config('app.name'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$pageTitle}}</title>
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            color: #fff;
            width: 240px;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 240px;
            padding: 20px;
        }
    </style>
    
	<link rel="stylesheet" href="{{asset('lib/fontawesome-6.7/css/all.min.css')}}" >
	<link rel="stylesheet" href="{{asset('lib/bootstrap5.3/bootstrap.min.css')}}" >
    <script type="text/javascript" src="{{asset('lib/jquery3.7/jquery.min.js')}}"></script>
</head>
<body>

<div class="sidebar">
    <h4 class="text-center">My Dashboard</h4>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{route('admin.dashboard')}}" class="nav-link">Dashboard</a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.events.index')}}" class="nav-link">Events</a>
        </li>
        {{-- <li class="nav-item">
            <a href="#" class="nav-link">Settings</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">Reports</a>
        </li> --}}
    </ul>
</div>

<div class="content">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('admin.dashboard')}}">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#">Profile</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('logout')}}">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>
</div>

<script src="{{asset('lib/bootstrap5.3/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
