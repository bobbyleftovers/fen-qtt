<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- favicon -->
        <!-- <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16" /> -->

        <title>FenQTT</title>

        <!-- Styles -->
        <!-- <link rel="stylesheet" type="text/css" href="{{ asset('fonts/aller.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('fonts/codepro.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('fonts/codepro-light.css') }}"/> -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        
        @yield('content')

        <script src="{{ asset('js/app.js') }}"></script>
    </body>
    
</html>
