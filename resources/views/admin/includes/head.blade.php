<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Surgical Equipments">
  <meta name="keyword" content="Surgical Equipments">
  <meta name="robots" content="noindex, nofollow" />
  <meta name="robots" content="noarchive" />
  <meta name="robots" content="noodp " />
  <meta name="robots" content="noydir" />

  <meta name="author" content="">
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>{{ config('app.name') }} | @yield('title')</title>
  <link rel="apple-touch-icon" href="{{ asset(Utility::THEME_ADMIN . 'images/apple-touch-icon.png') }}">
  <link rel="shortcut icon" href="{{ asset(Utility::THEME_ADMIN . 'images/favicon.ico') }}">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'bootstrap/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Web Icons -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'fonts/web-icons/web-icons.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'dist/css/skins/_all-skins.min.css') }}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/select2/select2.min.css') }}">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/iCheck/all.css') }}">

  <!--GLOBAL-->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'bootstrap/css/global.css') }}">
  <!--// GLOBAL //-->

  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/toastr/toastr.css') }}">

  <!-- LADDA -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'vendor/ladda/ladda-themeless.min.css') }}">
  <script src="{{ asset(Utility::THEME_ADMIN . 'vendor/ladda/spin.min.js') }}"></script>
  <script src="{{ asset(Utility::THEME_ADMIN . 'vendor/ladda/ladda.min.js') }}"></script>
  <!-- LADDA -->

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

<!-- ALERTIFY -->
<!-- JavaScript -->
<script src="{{ asset(Utility::THEME_ADMIN . 'vendor/alertifyjs/alertify.js') }}"></script>
<!-- CSS -->
<link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'vendor/alertifyjs/css/alertify.min.css') }}">
<!-- Default theme -->
<link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'vendor/alertifyjs/css/themes/bootstrap.min.css') }}"/>
<!--// ALERTIFY //-->
@stack('page_styles')
