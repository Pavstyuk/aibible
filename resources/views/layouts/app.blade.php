<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        @include('partials.head')
    </head>

    <body class="bible {{ $theme }}">

        @include('partials.header')

        <main class="container-narrow pt-2 pb-4 height-full">

            @yield('content')

        </main>

        @include('partials.footer')

    </body>

</html>
