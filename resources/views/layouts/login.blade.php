<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.header.meta')
    <title>
        {{ config('app.school-name') }} Club Management | Select Club
    </title>
    @include('partials.header.styles')
</head>
<body>
<div id="app">
    <main class="py-4">
        @yield ('content')
    </main>

    @include('partials.footer.footer')
</div>
</body>

@include('partials.footer.scripts')
</html>
