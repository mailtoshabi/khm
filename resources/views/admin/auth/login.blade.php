@extends('admin.layouts.out')

@section('title','Login')
@section('content')
    <div class="login-box">
        <div class="login-logo" style="font-size: 30px; margin-bottom: 7px;">
            <a href="#" >
            <img src="{{ asset(Utility::THEME_ADMIN . 'images/logo.png') }}" width="50%" alt="{{ config('app.name') }}" />
            {{--<b style="color: #2874f0">{{ config('app.name') }}</b>--}}
            </a>
            <p style="font-size: 29px;"><small>Admin Panel</small></p>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to enter the Admin Area</p>

            <form method="post" action="{{ route('admin.login') }}">
                {!! csrf_field() !!}
                <div class="form-group has-feedback @if ($errors->has('email')||$errors->has('invalid')) has-error  @endif">
                    <input type="text" class="form-control" placeholder="Email/Username" name="email" id="login_email" value="{{ old('email') }}">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    {{-- @if ($errors->has('email'))
                        <span class="help-block">
                            {{ $errors->first('email') }}
                        </span>
                    @endif --}}
                    @error('email')
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    @error('invalid')
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group has-feedback @if ($errors->has('password')) has-error  @endif">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    {{-- @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}
                        </span>
                    @endif --}}
                    @error('password')
                        <span class="help-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                {{--<input type="checkbox"> Remember Me--}}
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            {{--<div class="social-auth-links text-center">
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
                    Facebook</a>
                <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
                    Google+</a>
            </div>--}}
            <!-- /.social-auth-links -->

            {{--<a href="{{ url('/password/reset') }}">I forgot my password</a><br>--}}
            {{--<a href="register.html" class="text-center">Register a new membership</a>--}}
            <hr style="border-top: 1px solid #DBDADA;">
            <div class="text-center">
                <strong>Copyright &copy; 2018 <a href="{{ config('app.website_url') }}">{{ config('app.name') }}</a>.</strong>
                {{--Powered by <b><a href="https://webmahal.com" target="_blank">WEB MAHAL</a></b>--}}
            </div>
        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
@stop

@push('page_scripts')
<script>
    $(document).ready(function(){
        $('#login_email').focus().select();
    });
</script>
@endpush
