@extends('admin.layouts.default')
@section('title','Add Store')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ isset($store->id) ? 'Edit' : 'Add New' }} Store
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">{{ isset($store->id) ? 'Edit' : 'Add New' }} Store </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- form start -->
        <form action="@if(isset($store->id)) {{ route('admin.stores.update',[$store->id]) }} @else {{ route('admin.stores.store') }}  @endif" method="POST" role="form" id="add-store-form" enctype="multipart/form-data">
            {!! csrf_field() !!}
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        {{--<h3 class="box-title"> Store</h3>--}}
                    </div>
                    <!-- /.box-header -->

                        @if(isset($store->id))
                            <input type="hidden" name="_method" value="PUT" />
                        @endif
                        <div class="box-body">
                            <div class="row">
                                @if(isset($slug))
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" value="{{ config('app.website_url') }}/{{ $slug }}" disabled>
                                    </div>
                                @endif
                                {{--<div class="form-group col-sm-6 @if ($errors->has('username')) has-error @endif">
                                    <label class="control-label" for="username">username<span class="text-red">*</span></label>
                                    <div class="input-group">
                                        <div class="row">
                                            <div class="col-md-8 col-sm-8 col-lg-8 col-xs-8" style="padding-right: 0px">
                                                <input type="text" class="form-control" id="post_username" name="post_username" value="" placeholder="{{ config('app.website_url') }}/" disabled>
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-lg-4 col-xs-4" style="padding-left: 0px">
                                                <input type="text" class="form-control" id="username" name="username" placeholder="username"
                                                       autocomplete="off" value="{{ old('username', isset($slug) ? $slug : null) }}">
                                            </div>

                                        </div>
                                    </div>
                                    @if ($errors->has('username'))
                                        <span class="help-block">
                                            {{ $errors->first('username') }}
                                        </span>
                                    @endif
                                </div>--}}
                                <div class="form-group col-sm-6 @if ($errors->has('name')) has-error @endif">
                                    <label for="name">Name<span class="text-red">*</span> </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Store Name"
                                           value="{{ old('name', isset($store->name) ? $store->name : null) }}">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                    {{ $errors->first('name') }}
                                </span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>Image </label>
                                    <div id="dvUploadImage" class="{{ isset($store->id)&&!empty($store->image) ? 'hidden' : '' }}">
                                        <input type="file" id="image" name="image">
                                        <p class="help-block">Upload images with a size of 700x300</p>
                                    </div>
                                    @if(isset($store->id)&&!empty($store->image))
                                        <div id="dvImage">
                                            <div class="col-md-8 col-xs-7">
                                                <img src="{{ asset($store->image) }}" alt="" width="100%" />
                                            </div>
                                            <div class="col-md-1 col-xs-2">
                                                <a data-target="#dvImage" data-replace="#dvUploadImage" class="icon wb-close close_data"></a>
                                            </div>
                                        </div>
                                        <input type="hidden" name="is_image" id="is_image" value="1" >
                                    @endif
                                </div>
                                <div class="form-group col-sm-6">
                                    <label >Visiting Card</label>
                                    <div id="dvBrochureUpload" class="{{ isset($store->id)&&!empty($store->brochure) ? 'hidden' : '' }}">
                                        <input type="file" id="brochure" name="brochure">
                                        <p class="help-block">Upload images with a size of 340x200</p>
                                    </div>
                                    @if(isset($store->id)&&!empty($store->brochure))
                                        <div id="dvBrochure">
                                            <div class="col-md-8 col-xs-7">
                                                <img src="{{ asset($store->brochure) }}" alt="" width="100%" />
                                            </div>
                                            <div class="col-md-1 col-xs-2">
                                                <a data-target="#dvBrochure" data-replace="#dvBrochureUpload" class="icon wb-close close_data"></a>
                                            </div>
                                        </div>
                                        <input type="hidden" name="is_brochure" id="is_brochure" value="1" >
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('short_description')) has-error @endif">
                                    <label for="name">Short Description</label>
                                    <input class="form-control" id="short_description" name="short_description" placeholder="Enter Short Description" value="{{ old('short_description', isset($store->short_description) ? $store->short_description : null) }}">
                                    @if ($errors->has('short_description'))
                                        <span class="help-block">
                                        {{ $errors->first('short_description') }}
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 @if ($errors->has('description')) has-error @endif">
                                    <label for="description">Long Description</label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter Description">{{ old('description', isset($store->description) ? $store->description : null) }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                        {{ $errors->first('description') }}
                                    </span>
                                    @endif
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12 ">
                                    <label for="footer_description">About Us</label>
                                    <textarea class="form-control" id="footer_description" name="footer_description" placeholder="Enter Footer Description">{{ old('footer_description', isset($store->footer_description) ? $store->footer_description : null) }}</textarea>
                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="email">Email</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email"
                                           autocomplete="off" value="{{ old('email', isset($store->email) ? $store->email : null) }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter Phone"
                                           autocomplete="off" value="{{ old('phone', isset($store->phone) ? $store->phone : null) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="location">City</label>
                                    <input type="text" class="form-control" id="city" name="city" placeholder="Enter City"
                                           autocomplete="off" value="{{ old('city', isset($store->city) ? $store->city : null) }}">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="control-label" for="district">District</label>
                                    <input type="text" class="form-control" id="district" name="district" placeholder="Enter District"
                                           autocomplete="off" value="{{ old('district', isset($store->district) ? $store->district : null) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <label class="control-label" for="location">AddressLocation</label>
                                    <textarea class="form-control" id="location" name="location" placeholder="Enter Address"
                                              autocomplete="off">{{ old('location', isset($store->location) ? $store->location : null) }}</textarea>
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
                                                           autocomplete="off" value="{{ old('site_title', isset($store->id) ? $store->site_title : null) }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Keywords</label>
                                                    <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', isset($store->site_keywords) ? $store->site_keywords : null) }}</textarea>
                                                    @if ($errors->has('site_keywords'))
                                                        <span class="help-block">
                                        {{ $errors->first('site_keywords') }}
                                    </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Description</label>
                                                    <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', isset($store->site_description) ? $store->site_description : null) }}</textarea>
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
                            <button type="submit" class="btn btn-primary">@if(!isset($store->id)) Create @else Update @endif</button>
                        </div>

                </div>

            </div>

            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-list"></span> Pin Numbers
                    </div>
                    <div id="" class="panel-body">
                        <div class="form-group">
                            <label class="control-label">Pin Numbers</label>
                            <select class="form-control select2" id="pins" name="pins[]" style="width: 100%;" multiple data-placeholder="Select pin numbers">
                                @foreach($pinLists as $id => $pinList)
                                    <option {{ isset($pin_id_array) && in_array($id, $pin_id_array) ? 'selected' : '' }} value="{{ $id }}">{{ $pinList }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

            </div>


            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-list"></span> Categories
                    </div>
                    <div id="sidebar_categories" class="panel-body">

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

@push('page_scripts')
<script>
    $(document).ready(function() {

        var sidebar_categories = $('#sidebar_categories');

        @if(isset($store->id))

            var url = '{{ route('admin.stores.sidebar_categories',[$store->id]) }}';

        @else

            url = '{{ route('admin.stores.sidebar_categories') }}';

        @endif

        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                sidebar_categories.append(data.content);
            }
        });

        $('#dvUploadImage').on('close_data.success', function (e) {
            $('#is_image').val(0);
        });
        $('#dvBrochureUpload').on('close_data.success', function (e) {
            $('#is_brochure').val(0);
        });

        jQuery.validator.addMethod("noSpecialChar", function(value, element) {
            return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
        }, "Please Fill Correct Value in the Field.");

        jQuery.validator.addMethod("noSpace", function(value, element) {
            return value.indexOf(" ") < 0 && value != "";
        }, "Space are not allowed");

        var $validator = $('#add-store-form').validate({
            rules: {
                name: {
                    required: true
                },
                                image: {
                    extension: "jpg|png"
                },
                brochure: {
                    extension: "jpg|png"
                },
                unit_om: {
                    required: true
                }/*,
                username: {
                    required: true,
                    noSpecialChar : true,
                    noSpace : true
                }*/
            },
            messages: {
                name: {
                    required: "Name is required"
                },
                image: {
                    extension: "Image must be jpg or png"
                },
                brochure: {
                    extension: "Image must be in jpg or png"
                }/*,
                username: {
                    required: "username is required"
                }*/
            }
        });

    });
</script>
<script>
    $(function () {
        //bootstrap WYSIHTML5 - text editor
        $("#description").wysihtml5();
        $("#footer_description").wysihtml5();
    });
</script>
@endpush
