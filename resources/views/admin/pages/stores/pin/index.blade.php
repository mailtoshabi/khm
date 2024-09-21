@extends('admin.layouts.list')
@section('title','Pin Numbers')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pin Numbers
        <small>List of All Stores</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Pin Numbers</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <a href="#" data-plugin="render-modal" data-modal="#dvAdd-pin" data-target="{{ route('admin.stores.pins.create_modal') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="pins-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Action</th>
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
@include('admin.pages.stores.pin.add-modal')
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function(){
            var dbTable = $('#pins-table');
            var pageModal = $('#dvAdd-pin');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.stores.pins.data') }}",
                        columns: [
                            {data: 'name', name: 'name'},
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

            pageModal.on('af.success','#add-pin-form',function() {
                $('#dvAdd-pin').fadeOut(350);
                $(this).trigger('hidden.bs.modal');
                pageModal.modal('hide');
                dbTableColumns.draw();
            });

            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-pin-form').attr('id');

                jQuery.validator.addMethod("noSpecialChar", function(value, element) {
                    return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
                }, "Invalid PIN");

                jQuery.validator.addMethod("noSpace", function(value, element) {
                    return value.indexOf(" ") < 0 && value != "";
                }, "Space are not allowed");

                var $validator = $(formid).validate({
                    rules : {
                        name : {
                            required : true,
                            digits : true,
                            noSpecialChar : true,
                            noSpace : true,
                            minlength : 6
                        }
                    },
                    messages: {
                        name: {
                            required: "Enter PIN Number",
                            digits: "Invalid PIN",
                            minlength: "Invalid PIN"
                        }
                    }
                });

            });




        });
    </script>
@endpush
