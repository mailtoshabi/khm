@extends('admin.layouts.list')
@section('title','Sliders')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sliders
        <small>List of All Product Sliders</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Sliders</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <a href="#" data-plugin="render-modal" data-modal="#dvAdd-slider" data-target="{{ route('admin.sliders.create_modal') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="sliders-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>Order</th>
                            <th>Image</th>
                            <th style="min-width: 65px;">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop

@push('post_body')
@include('admin.pages.sliders.add-modal')
@endpush

@push('page_styles')
<style>
    .select2-container {
        z-index: 999999;
    }
</style>
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function(){
            var dbTable = $('#sliders-table');
            var pageModal = $('#dvAdd-slider');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.sliders.data') }}",
                        columns: [
                            {data: 'type', name: 'type'},
                            {data: 'order_no', name: 'order_no'},
                            {data: 'image', name: 'image'},
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                className: 'actions text-right'
                            }
                        ]
                    });

            dbTable.on('ajax-get-request.success', function (e,data) {
                e.preventDefault();
                dbTableColumns.draw();
            });

            pageModal.on('af.success','#add-slider-form',function() {
                $('#dvAdd-slider').fadeOut(350);
                $(this).trigger('hidden.bs.modal');
                pageModal.modal('hide');
                dbTableColumns.draw();
            });

            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-slider-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        type : {
                            required: true
                        },
                        order_no : {
                            required: true
                        },
                        image : {
                            required : true
                        }
                    },
                    messages: {

                    }
                });


            });




        });
    </script>
@endpush
