@extends('admin.layouts.default')
@section('title','Add Meta Tags')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{ isset($user->id) ? 'Edit' : 'Add New' }} Meta Tags
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Manage Meta Tags </li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <!-- form start -->
        <form action="{{ route('admin.affiliate.update.meta') }}" method="POST" role="form" id="add-affiliate-form" >
            {!! csrf_field() !!}
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        {{--<h3 class="box-title"> Meta Tags</h3>--}}
                    </div>
                    <!-- /.box-header -->

                        <input type="hidden" name="_method" value="PUT" />
                        <div class="box-body">

                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="dv_seotools" class="">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="control-label">UPI ID</label>
                                                    <input type="text" class="form-control" id="upi_id" name="upi_id" placeholder="Enter UPI ID"
                                                           autocomplete="off" value="{{ old('upi_id', $user->affiliate->upi_id) }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">G Pay</label>
                                                    <input type="text" class="form-control" id="g_pay" name="g_pay" placeholder="Enter G Pay" value="{{ old('g_pay', $user->affiliate->g_pay) }}" autocomplete="off">
                                                    @if ($errors->has('g_pay'))
                                                        <span class="help-block">
                                                            {{ $errors->first('g_pay') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Bank Account Details</label>
                                                    <textarea class="form-control" id="bank_account" name="bank_account" placeholder="Enter Bank Account Details">{{ old('bank_account', $user->affiliate->bank_account) }}</textarea>
                                                    @if ($errors->has('bank_account'))
                                                        <span class="help-block">
                                                {{ $errors->first('bank_account') }}
                                            </span>
                                                    @endif
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
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Title</label>
                                                    <input type="text" class="form-control" id="site_title" name="site_title" placeholder="Enter Site Title"
                                                           autocomplete="off" value="{{ old('site_title', $user->affiliate->site_title) }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Keywords</label>
                                                    <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', $user->affiliate->site_keywords) }}</textarea>
                                                    @if ($errors->has('site_keywords'))
                                                        <span class="help-block">
                                                            {{ $errors->first('site_keywords') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="control-label">Site Description</label>
                                                    <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', $user->affiliate->site_description) }}</textarea>
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
                            <button type="submit" class="btn btn-primary">Submit</button>
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

    });
</script>
<script>
    $(function () {
        //bootstrap WYSIHTML5 - text editor
        $("#bank_account").wysihtml5();
        $("#footer_description").wysihtml5();
    });

</script>
@endpush
