<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta name="author" content="Web Mahal Web Services">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title')</title>

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

    <!-- Bootstrap c0ore CSS -->
    <link href="{{ asset('clinic/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="{{ asset('clinic/fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">

    <!-- Plugin CSS -->
    <link href="{{ asset('clinic/vendor/magnific-popup/magnific-popup.css') }}" rel="stylesheet" type="text/css">

    <!-- Custom styles for this template -->
    <link href="{{ asset('clinic/css/freelancer.min.css') }}" rel="stylesheet">

    <link href="{{ asset('clinic/css/social_share.css') }}" rel="stylesheet">
    <link href="{{ asset('clinic/css/global.css') }}" rel="stylesheet">
    @stack('page_style')
</head>

<body id="page-top">
<div id="jq-loader">
    <img src="{{ asset('images/jq-loader/jq-loader.gif') }}" alt="">
</div>
@yield('content')

<!-- Bootstrap core JavaScript -->
{{--<script src="{{ asset('clinic/vendor/jquery/jquery.min.js') }}"></script>--}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="{{ asset('clinic/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Plugin JavaScript -->
<script src="{{ asset('clinic/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('clinic/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>

<!-- Contact Form JavaScript -->
<script src="{{ asset('clinic/js/jqBootstrapValidation.js') }}"></script>
<script src="{{ asset('clinic/js/contact_me.js') }}"></script>

<!-- Custom scripts for this template -->
<script src="{{ asset('clinic/js/freelancer.min.js') }}"></script>
<script>
    // highlight current day on opeining hours
    $(document).ready(function() {
        $('.opening-hours li').eq(new Date().getDay()-1).addClass('today');
    });
</script>
@stack('page_scripts')
</body>

</html>