    @extends('admin.layouts.list')
@section('title','Subscribers')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Subscribers
        <small>List of All Subscribers</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Subscribers</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="subscribers-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Email</th>
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
            var dbTable = $('#subscribers-table');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.subscriber.data') }}",
                        columns: [
                            {data: 'phone', name: 'phone'}
                        ]
                    });

        });
    </script>
@endpush
