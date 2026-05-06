<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body id="top" class="home {{ $theme }}">

    @include('partials.header')

    <main class="container-wide pt-2 pb-4 height-full">

        @yield('home')

    </main>

    @include('partials.footer')

</body>

</html>
