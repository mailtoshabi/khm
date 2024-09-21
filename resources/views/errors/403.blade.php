@extends('layouts.error')

@section('title','403')
@section('content')
    <h1>
        Oops!</h1>
    <h2>
        403 Not Found</h2>
    <div class="error-details">
        Sorry, an error has occured, Requested page not found!
    </div>
    <div class="error-actions">
        <a href="{{ route('index') }}" class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-home"></span>
            Take Me Home </a><a href="{{ route('contact') }}" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-envelope"></span> Contact Support </a>
    </div>
    @stop
