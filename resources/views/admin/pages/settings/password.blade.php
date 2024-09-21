@extends('admin.layouts.default')
@section('title','Settings')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Settings
        <small>Change your admin password.</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Settings</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Change Password</h3>
            </div>
            <!-- /.box-header -->

            <!-- form start -->
            <form method="POST" action="{{ route('admin.settings.password.update') }}">
              {{ csrf_field() }}
              <input type="hidden" name="_method" value="PUT" />
              <div class="box-body">
                <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                  <label for="current_password">Current Password</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter Current Password">
                  @if ($errors->has('current_password'))
                    <span class="help-block">
                        {{ $errors->first('current_password') }}
                    </span>
                  @endif
                </div>
                <div class="form-group">
                  <label for="name">New Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Enter New Password">
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                  <label for="contact_person">Confirm Password</label>
                  <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Enter Password Again">
                  @if ($errors->has('password'))
                    <span class="help-block">
                        {{ $errors->first('password') }}
                    </span>
                  @endif
                </div>

              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Change</button>
              </div>
            </form>
          </div>

          </div>
          <!-- /. box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->


@stop
