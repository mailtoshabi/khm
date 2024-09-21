@extends('admin.layouts.default')
@section('title','General Settings')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        General Settings
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">General Settings</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- form start -->
        <form action="{{ route('admin.settings.general.update') }}" method="POST" role="form" id="general-settings-form" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT" />
            <div class="col-md-8">
                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title"> Contact Settings</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6 @if ($errors->has('admin_email')) has-error @endif">
                                <label for="name">Admin Email<span class="text-red">*</span> </label>
                                <input type="text" class="form-control" id="admin_email" name="admin_email" placeholder="Enter Email"
                                       value="{{ Utility::settings('admin_email') }}">
                                @if ($errors->has('admin_email'))
                                    <span class="help-block">
                                    {{ $errors->first('admin_email') }}
                                </span>
                                @endif
                            </div>
                            <div class="form-group col-sm-6 @if ($errors->has('admin_phone')) has-error @endif">
                                <label for="admin_phone">Admin Phone<span class="text-red">*</span> </label>
                                <input type="text" class="form-control" id="admin_phone" name="admin_phone" placeholder="Enter Phone"
                                       value="{{ Utility::settings('admin_phone') }}">
                                @if ($errors->has('admin_phone'))
                                    <span class="help-block">
                                    {{ $errors->first('admin_phone') }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="box-header with-border">
                        <h3 class="box-title"> Delivery Charge Settings</h3>
                    </div>
                    <!-- /.box-header -->
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label for="name">Minimum Amount for Free Delivery</label>
                                    <input type="text" class="form-control" id="minimum_to_delivery_charge" name="minimum_to_delivery_charge" placeholder="Enter Value"
                                           value="{{ Utility::settings('minimum_to_delivery_charge') }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="name">Delivery Charge</label>
                                    <input type="text" class="form-control" id="delivery_charge" name="delivery_charge" placeholder="Enter Value"
                                           value="{{ Utility::settings('delivery_charge') }}">
                                </div>
                            </div>

                            {{--<div class="row">
                                <div class="form-group col-sm-6">
                                    <input type="checkbox" class="form-control" id="is_featured" name="is_featured" value="1" >
                                    &nbsp;&nbsp;&nbsp;<label for="is_featured">Featured product</label>
                                </div>
                            </div>--}}

                        </div>
                        <!-- /.box-body -->

                    {{-- <div class="box-header with-border">
                        <h3 class="box-title"> SMS Settings</h3>
                    </div> --}}

                    {{-- <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="smsapi_user">SMS  API Username</label>
                                <input type="text" class="form-control" id="smsapi_user" name="smsapi_user" placeholder="API Username"
                                       value="{{ Utility::settings('smsapi_user') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="smsapi_password">SMS API Password</label>
                                <input type="password" class="form-control" id="smsapi_password" name="smsapi_password" placeholder="API Password"
                                       value="{{ Utility::settings('smsapi_password') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="smsapi_sender">Sender ID</label>
                                <input type="text" class="form-control" id="smsapi_sender" name="smsapi_sender" placeholder="API Sender ID"
                                       value="{{ Utility::settings('smsapi_sender') }}">
                            </div>
                        </div>
                    </div> --}}

                    <div class="box-header with-border">
                        <h3 class="box-title"> Bank Settings</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="smsapi_user">Account Name</label>
                                <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account Name"
                                       value="{{ Utility::settings('account_name') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="account_number">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account Number"
                                       value="{{ Utility::settings('account_number') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name"
                                       value="{{ Utility::settings('bank_name') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="ifsc_code">IFSC Code</label>
                                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" placeholder="IFSC Code"
                                       value="{{ Utility::settings('ifsc_code') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="bank_branch">Bank Branch </label>
                                <input type="text" class="form-control" id="bank_branch" name="bank_branch" placeholder="Bank Branch"
                                       value="{{ Utility::settings('bank_branch') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="upi_id">UPI ID</label>
                                <input type="text" class="form-control" id="upi_id" name="upi_id" placeholder="UPI ID"
                                       value="{{ Utility::settings('upi_id') }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="google_pay">Google Pay</label>
                                <input type="text" class="form-control" id="google_pay" name="google_pay" placeholder="Google Pay"
                                       value="{{ Utility::settings('google_pay') }}">
                            </div>
                        </div>
                    </div>

                    <div class="box-header with-border">
                        <h3 class="box-title"> General SEO Settings</h3>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label for="smsapi_user">Site Title</label>
                                <input type="text" class="form-control" id="site_title" name="site_title" placeholder="Site Title"
                                       value="{{ Utility::settings('site_title') }}">
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="smsapi_password">Site Keywords</label>
                                <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Site Description">{{ Utility::settings('site_keywords') }}</textarea>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="smsapi_sender">Site Description</label>
                                <textarea class="form-control" id="site_description" name="site_description" placeholder="Site Description">{{ Utility::settings('site_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Update </button>
                        </div>

                </div>

            </div>


        </form>
        <!-- /. box -->
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@push('post_body')

@endpush


@push('page_scripts')
<script>
    $(document).ready(function() {


    });
</script>
@endpush
