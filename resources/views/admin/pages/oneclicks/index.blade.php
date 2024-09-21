    @extends('admin.layouts.list')
@section('title','One Click Purchases')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        One Click Purchases
        <small>List of All One Click Purchases</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">One Click Purchases</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="oneclick-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Phone</th>
                            <th>Product</th>
                            <th>Sale Through</th>
                            <th>Status</th>
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
            var dbTable = $('#oneclick-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.oneclick_purchase.data') }}",
                        columns: [
                            {data: 'created_at', name: 'created_at', searchable: true},
                            {data: 'phone', name: 'phone',orderable: false},
                            {
                                data: 'product_id',
                                name: 'product_id',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'user_id',
                                name: 'user_id',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'is_active',
                                name: 'is_active',
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



        });
    </script>
@endpush
