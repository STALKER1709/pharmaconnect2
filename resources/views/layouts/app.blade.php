<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="bg-[#f0fdf6] font-body-md text-body-md text-on-surface antialiased">
    @include('partials.entete-public')

    <main class="@yield('classe_main', 'w-full pt-20 bg-[#f0fdf6] min-h-[calc(100vh-20rem)]')">
        @yield('contenu')
    </main>

    @include('partials.pied-public')
    @include('partials.flash')
    @yield('scripts')
</body>
</html>
