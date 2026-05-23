<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body id="top" class="bible {{ $theme }}" data-font-size="{{ $_COOKIE['font_size'] ?? '' }}">

    @include('partials.header')

    <main class="container-narrow pt-2 pb-4 height-full">

        @yield('content')

    </main>

    @include('partials.footer')

</body>

</html>
