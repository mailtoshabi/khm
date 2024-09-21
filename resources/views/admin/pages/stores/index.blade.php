@extends('admin.layouts.list')
@section('title','Stores')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Stores
        <small>List of All Stores</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Stores</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    {{--<h3 class="box-title">Products List</h3>--}}
                    <a href="{{ route('admin.stores.create') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="stores-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            {{--<th>User Name</th>--}}
                            <th>City</th>
                            <th>Pin Numbers</th>
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
            var dbTable = $('#stores-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.stores.data') }}",
                        columns: [
                            {data: 'name', name: 'name'},
                            /*{data: 'username', name: 'username'},*/
                            {data: 'city', name: 'city'},
                            {data: 'pins', name: 'pins'},
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
