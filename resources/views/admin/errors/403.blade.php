@extends('admin.layouts.out')

@section('title','403')
@section('content')
    <div class="login-box">
        <div class="login-logo" style="font-size: 30px; margin-bottom: 7px;">
            <a href="#" >
                <img src="{{ asset(Utility::THEME_ADMIN . 'images/logo.png') }}" width="50%" alt="{{ config('app.name') }}" />
            </a>
            <p style="font-size: 29px;"><small>Admin Panel</small></p>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <header>
                <h1 class="animation-slide-top">403</h1>
                <p>Access Denied !</p>
            </header>
            <p class="error-advise">YOU DO NOT HAVE PERMISSION TO ACCESS THE PAGE YOU REQUESTED</p>
            <a class="btn btn-primary btn-round" href="{{ route('admin.index') }}">Be right back.</a>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    @stop
