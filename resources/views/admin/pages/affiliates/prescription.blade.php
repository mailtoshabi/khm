    @extends('admin.layouts.list')
@section('title','Prescriptions')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Prescriptions
        <small>List of All Prescriptions</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Prescriptions</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="prescription-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Phone</th>
                            <th>Prescriptions</th>
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
            var dbTable = $('#prescription-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.affiliate.prescriptions.data') }}",
                        columns: [
                            {data: 'created_at', name: 'created_at', searchable: true},
                            {data: 'phone_prescription', name: 'phone_prescription',orderable: false},
                            {
                                data: 'image_prescription',
                                name: 'image_prescription',
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
