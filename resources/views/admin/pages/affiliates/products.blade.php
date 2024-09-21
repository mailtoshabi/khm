@extends('admin.layouts.list')
@section('title','Products')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Products
        <small>List of All Products</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Products</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">

                <!-- /.box-header -->
                <div class="box-body">
                    <table id="products-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Categories</th>
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
            var dbTable = $('#products-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.affiliate.products.data') }}",
                        columns: [
                            {data: 'name', name: 'name'},
                            {
                                data: 'categories',
                                name: 'categories',
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

            /*dbTable.on('ajax-get-request.success', function (e,data) {
                e.preventDefault();
                if(data.target == 1) {
                    window.location.replace(data.success_route);
                }else {
                    dbTableColumns.draw();
                }

            });*/

            dbTable.on('ajax-get-request.success', function (e,data) {
                e.preventDefault();
                dbTableColumns.draw();
            });

        });
    </script>
@endpush
