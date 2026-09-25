<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('titre', 'Accueil') — PharmaConnect</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('scripts')
