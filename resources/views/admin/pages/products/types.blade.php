    @extends('admin.layouts.list')
@section('title','Product Types')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Types
        <small>List of All Product Types</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Types</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <a href="#" data-plugin="render-modal" data-modal="#myTypeSizeModal" data-target="{{ route('admin.products.types.create_modal') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="product-type-table" class="table table-bordered table-striped ">
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
@include('admin.pages.products.type-size-modal')
@endpush


@push('page_scripts')
    <script>
        $(document).ready(function(){
            var dbTable = $('#product-type-table');
            var pageModal = $('#myTypeSizeModal');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.products.types.data') }}",
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

            pageModal.on('af.success','#add-type-size-form',function() {
                $('#dvAdd-category').fadeOut(350);
                $(this).trigger('hidden.bs.modal');
                pageModal.modal('hide');
                dbTableColumns.draw();
            });



            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-type-size-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        name : {
                            required : true
                        }
                    },
                    messages: {
                        name: "Enter Product Type Name"
                    }
                });


            });

        });
    </script>
@endpush
