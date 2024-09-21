<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta name="author" content="Web Mahal Web Services">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title')</title> {{-- | {{ config('app.name') }}--}}

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('store/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="{{ asset('store/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>

    <!-- Plugin CSS -->
    <link href="{{ asset('store/vendor/magnific-popup/magnific-popup.css') }}" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="{{ asset('store/css/creative.min.css') }}" rel="stylesheet">
    <link href="{{ asset('store/css/global.css') }}" rel="stylesheet">
    @stack('page_style')
</head>

<body id="page-top">

@yield('content')
        <!-- Bootstrap core JavaScript -->
<script src="{{ asset('store/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('store/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Plugin JavaScript -->
<script src="{{ asset('store/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('store/vendor/scrollreveal/scrollreveal.min.js') }}"></script>
<script src="{{ asset('store/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

<!-- Custom scripts for this template -->
<script src="{{ asset('store/js/creative.min.js') }}"></script>

@stack('page_scripts')
</body>

</html>