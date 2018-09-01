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
        {{ config('app.school-name') }} Club Management | Select Club</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    @stack('styles')
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <main class="py-4">
        @yield ('content')
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
