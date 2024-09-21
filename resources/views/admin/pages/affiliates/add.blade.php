@extends('admin.layouts.default')
@section('title','Add Affiliate')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ isset($user->id) ? 'Edit' : 'Add New' }} Affiliate
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">{{ isset($user->id) ? 'Edit' : 'Add New' }} Affiliate </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- form start -->
        <form action="@if(isset($user->id)) {{ route('admin.affiliates.update',[$user->id]) }} @else {{ route('admin.affiliates.store') }}  @endif" method="POST" role="form" id="add-affiliate-form" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        {{--<h3 class="box-title"> Affiliate</h3>--}}
                    </div>
                    <!-- /.box-header -->

                        @if(isset($user->id))
                            <input type="hidden" name="_method" value="PUT" />
                        @endif
                        <div class="box-body">
                            @if(isset($slug))
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" value="{{ config('app.website_url') }}/{{ $slug }}" disabled>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="form-group col-sm-6 @if ($errors->has('email')) has-error @endif">
                                    <label for="email">Email (User Name)</label>
                                    <input class="form-control" id="email" name="email" placeholder="Enter User Name" value="{{ old('email', isset($user->email) ? $user->email : null) }}" onkeyup="getUserName(this.value)">
                                    <input type="hidden" class="form-control" id="username" name="username" value="{{ isset($user->username) ? $user->username : null }}">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        {{ $errors->first('email') }}
                                    </span>
                                    @endif
                                </div>
                                {{--@if(!isset($user->id))--}}
                                    <div class="form-group col-sm-6 @if ($errors->has('password')) has-error @endif">
                                        <label for="password">Password</label>
                                        <input class="form-control" id="password" name="password" placeholder="{{ isset($user->password) ? "Leave Blank If no change" : "Password" }}" >
                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                            {{ $errors->first('password') }}
                                        </span>
                                        @endif
                                    </div>
                                {{--@endif--}}
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('name')) has-error @endif">
                                    <label for="name">Name<span class="text-red">*</span> </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Affiliate Name"
                                           value="{{ old('name', isset($user->name) ? $user->name : null) }}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                    {{ $errors->first('name') }}
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label>Logo Image </label>
                                    <div id="dvUploadImage" class="{{ isset($user->id)&&!empty($user->affiliate->image) ? 'hidden' : '' }}">
                                        <input type="file" id="image" name="image">
                                        <p class="help-block">Upload image with a size of {{ Utility::IMAGE_AFFILIATE_ORIGINAL }}</p>
                                    </div>
                                    @if(isset($user->id)&&!empty($user->affiliate->image))
                                        <div id="dvImage">
                                            <div class="col-md-8 col-xs-7">
                                                {{-- <img src="{{ asset($user->affiliate->image['thumb']) }}" alt="" /> --}}
                                                <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Affiliate::FILE_DIRECTORY .  '/' . $user->affiliate->image['original']) }}" target="_blank">
                                                    <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Affiliate::FILE_DIRECTORY .  '/' . $user->affiliate->image['thumb']) }}" alt="" height="50" />
                                                </a>
                                            </div>
                                            <div class="col-md-1 col-xs-2">
                                                <a data-target="#dvImage" data-replace="#dvUploadImage" class="icon wb-close close_data"></a>
                                            </div>
                                        </div>
                                        <input type="hidden" name="is_image" id="is_image" value="1" >
                                    @endif
                                </div>

                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('description')) has-error @endif">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{ old('description', isset($user->affiliate->description) ? $user->affiliate->description : null) }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        {{ $errors->first('description') }}
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 @if ($errors->has('location')) has-error @endif">
                                    <label for="location">Location</label>
                                    <input class="form-control" id="location" name="location" placeholder="Enter Location" value="{{ old('location', isset($user->affiliate->location) ? $user->affiliate->location : null) }}">
                                    @if ($errors->has('location'))
                                        <span class="help-block">
                                        {{ $errors->first('location') }}
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group col-sm-6 @if ($errors->has('city')) has-error @endif">
                                    <label for="city">City</label>
                                    <input class="form-control" id="city" name="city" placeholder="Enter City" value="{{ old('city', isset($user->affiliate->city) ? $user->affiliate->city : null) }}">
                                    @if ($errors->has('city'))
                                        <span class="help-block">
                                        {{ $errors->first('city') }}
                                    </span>
                                    @endif
                                </div>

                            </div>

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="district">District</label>
                                    <input type="text" class="form-control" id="district" name="district" placeholder="Enter District"
                                           autocomplete="off" value="{{ old('district', isset($user->affiliate->district) ? $user->affiliate->district : null) }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="pin">Pin</label>
                                    <input type="text" class="form-control" id="pin" name="pin" placeholder="Enter Pin"
                                           autocomplete="off" value="{{ old('pin', isset($user->affiliate->pin) ? $user->affiliate->pin : null) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label class="control-label" for="contact_email">Contact Email</label>
                                    <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="Enter Email"
                                           autocomplete="off" value="{{ old('contact_email', isset($user->affiliate->contact_email) ? $user->affiliate->contact_email : null) }}">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="control-label" for="contact_phone">Contact Phone</label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" placeholder="Enter Phone"
                                           autocomplete="off" value="{{ old('contact_phone', isset($user->affiliate->contact_phone) ? $user->affiliate->contact_phone : null) }}">
                                </div>
                                <div class="form-group col-sm-4">
                                    <label class="control-label" for="contact_whatsapp">Contact Whatsapp</label>
                                    <input type="text" class="form-control" id="contact_whatsapp" name="contact_whatsapp" placeholder="Enter Whatsapp"
                                           autocomplete="off" value="{{ old('contact_whatsapp', isset($user->affiliate->contact_whatsapp) ? $user->affiliate->contact_whatsapp : null) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('footer_description')) has-error @endif">
                                    <label for="footer_description">Footer Description</label>
                                    <textarea class="form-control" id="footer_description" name="footer_description" placeholder="Enter Description">{{ old('footer_description', isset($user->affiliate->footer_description) ? $user->affiliate->footer_description : null) }}</textarea>
                                    @if ($errors->has('footer_description'))
                                        <span class="help-block">
                                    {{ $errors->first('footer_description') }}
                                </span>
                                    @endif
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="control-label">SEO Tools</label>
                                    <div id="dv_seotools" class="well">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Title</label>
                                                    <input type="text" class="form-control" id="site_title" name="site_title" placeholder="Enter Site Title"
                                                           autocomplete="off" value="{{ old('site_title', isset($user->affiliate->id) ? $user->affiliate->site_title : null) }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Keywords</label>
                                                    <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', isset($user->affiliate->site_keywords) ? $user->affiliate->site_keywords : null) }}</textarea>
                                                    @if ($errors->has('site_keywords'))
                                                        <span class="help-block">
                                        {{ $errors->first('site_keywords') }}
                                    </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Description</label>
                                                    <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', isset($user->affiliate->site_description) ? $user->affiliate->site_description : null) }}</textarea>
                                                    @if ($errors->has('site_description'))
                                                        <span class="help-block">
                                                {{ $errors->first('site_description') }}
                                            </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">@if(!isset($user->id)) Create @else Update @endif</button>
                        </div>

                </div>

            </div>

        </form>
        <!-- /. box -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop

@push('page_scripts')
<script>
    $(document).ready(function() {


        $('#dvUploadImage').on('close_data.success', function (e) {
            $('#is_image').val(0);
        });

        jQuery.validator.addMethod("noSpecialChar", function(value, element) {
            return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
        }, "Please Fill Correct Value in the Field.");

        jQuery.validator.addMethod("noSpace", function(value, element) {
            return value.indexOf(" ") < 0 && value != "";
        }, "Space are not allowed");

        var $validator = $('#add-affiliate-form').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                },
                name: {
                    required: true
                }/*,
                'images[]': {
                    maxupload: 4,
                    extension: "jpg|jpeg|png"
                }*/
            },
            messages: {
                email: {
                    required: "Email is required"
                },
                name: {
                    required: "Name is required"
                }
            }
        });

        $(document).on("ifChanged", "input[name='is_oh']", function(e) {
            /*console.log('test');*/
            $("#dv_open_hrs").toggle(this.checked);

        });

    });
</script>
<script>
    $(function () {
        //bootstrap WYSIHTML5 - text editor
        $("#description").wysihtml5();
        $("#footer_description").wysihtml5();
    });

    function getUserName (email) {
          /*var username = email.substring(0, email.lastIndexOf("@"));*/
        $('#username').val(email);
    }
</script>
@endpush
