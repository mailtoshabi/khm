@extends('admin.layouts.list')
@section('title','Cities')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Cities
        <small>List of All Product Cities</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Cities</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <a href="#" data-plugin="render-modal" data-modal="#dvAdd-city" data-target="{{ route('admin.cities.create_modal') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="cities-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>District</th>
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
@include('admin.pages.cities.add-modal')
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
            var dbTable = $('#cities-table');
            var pageModal = $('#dvAdd-city');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.cities.data') }}",
                        columns: [
                            {data: 'name', name: 'name'},
                            {
                                data: 'district_id',
                                name: 'district_id',
                                orderable: false,
                                searchable: false
                            },
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

            pageModal.on('af.success','#add-city-form',function() {
                $('#dvAdd-city').fadeOut(350);
                $(this).trigger('hidden.bs.modal');
                pageModal.modal('hide');
                dbTableColumns.draw();
            });






            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-city-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        district_id : {
                            required : true
                        },
                        name : {
                            required : true
                        }
                    },
                    messages: {
                        district_id: "Select District",
                        name: "Enter City Name"
                    }
                });


            });




        });
    </script>
@endpush
