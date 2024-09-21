@extends('admin.layouts.list')
@section('title','Products')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Customers
        <small>List of All Customers</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Customers</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                {{--<div class="box-header">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>--}}
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="customers-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
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

@push('page_scripts')
    <script>
        $(document).ready(function(){
            var dbTable = $('#customers-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.customers.data') }}",
                        columns: [
                            {data: 'name', name: 'name',orderable: false,searchable: false},
                            {data: 'email', name: 'email'},
                            {data: 'phone', name: 'phone'},
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
        });
    </script>
@endpush
