@extends('admin.layouts.list')
@section('title','Sales')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sales
        <small>List of All Sales</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Sales</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header" id="dvstatus">
                    <select class="form-group select2" name="status" id="status" style="width: 300px" >
                        <option value="">Select Status</option>
                        @foreach(Utility::saleStatus() as $value => $sale_status)
                            <option value="{{ $value }}" {{ $value==Utility::SALE_STATUS_NEW ? 'selected' : '' }} >{{ $sale_status }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="sales-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Order Details</th>
                            <th>Sub Total</th>
                            <th>Status</th>
                            <th>Payment</th>
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
            var dbTable = $('#sales-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        dom: "trip",
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        /*ajax: "",*/
                        ajax: {
                            url: "{{ route('admin.affiliate.sales.data') }}",
                            data: function (d) {
                                    d.status = $('#status').val();
                                    /*var formData = $("#search-form").serializeJSON();
                                    $.extend(d, formData);*/
                            }
                        },
                        columns: [
                            {data: 'order_no', name: 'order_no'},
                            {data: 'sub_total', name: 'sub_total'},
                            {data: 'status', name: 'status'},
                            {data: 'payment', name: 'payment'},
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                className: 'actions text-right'
                            }
                        ]
                    });

            $('#dvstatus').on('change','#status',function (e) {
                dbTableColumns.draw();
            });

        });
    </script>
@endpush
