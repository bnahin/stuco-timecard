<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="ECRCHS Student Council Timecard System">
    <meta name="author" content="Blake Nahin">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ config('app.school-name')." ". ($clubName ?: 'Club Management') }}
        | @yield('page-title', 'Home') </title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    @stack('styles')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ $clubName }} @if(isAdmin()) Management @else Timecard @endif @if(app()->isLocal()) [DEV] @endif
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Nav Links -->
                    <li class="nav-item {{ (Route::currentRouteName() == "home") ? "active":"" }}">
                        <a class="nav-link" href="/">Home </a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endguest
                    @auth('user')
                        <li class="nav-item {{ (Route::currentRouteName() == "my-hours") ? "active":"" }}">
                            <a class="nav-link" href=" {{ route('my-hours') }}"><i class="fas fa-clock"></i> My
                                Hours</a>
                        </li>
                    @elseauth('admin')
                        <li class="nav-item {{ (Route::currentRouteName() == "admin") ? "active":"" }}">
                            <a class="nav-link" href="{{ route('admin') }}"><i
                                    class="fas fa-cogs"></i> Admin
                                @if($adminBadge) <span
                                    class="badge badge-success marked-badge">{{ $adminBadge }}</span> @endif
                            </a>
                        </li>
                    @endauth
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-auth="{{ (isAdmin()) ? "admin" : "user" }}"><i
                                class="fas fa-life-ring"></i> Help</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i> {{ Auth::user()->full_name }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" style="color:red">
                                <strong> <i class="fas fa-sign-out-alt"></i>
                                    Sign Out/Switch Club</strong></a>

                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <footer>
        <div class="container">
        <span class="pull-left">
            <strong>Club Management System v0.1a</strong>
        </span>
            <span class="pull-right">
        Created for ECRCHS by Blake Nahin (Class of 2019)
        <br>
        <a href="https://github.com/bnahin/club-management" target="_blank" rel="tooltip" title="Open Source @ GitHub">
            <i class="fab fa-github"></i>
        </a> | <a href="https://laravel.com" target="_blank" rel="tooltip"
                  title="Proudly made with the Laravel Framework"><span class="fab fa-laravel"></span></a>
        </span>
        </div>
    </footer>
</div>
</body>

<!-- Scripts -->
<script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.bundle.min.js"
        integrity="sha384-pjaaA8dDz/5BgdFUPX6M/9SUZv4d12SUPF0axWc+VRZkx5xU3daN+lYb49+Ax+Tl"
        crossorigin="anonymous"></script>

@stack('scripts')

<script src="{{ asset('js/app.js') }}" defer></script>
</html>
