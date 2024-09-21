    @extends('admin.layouts.list')
@section('title','Banners')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Banners
        <small>List of All Product Banners</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Banners</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <a href="#" data-plugin="render-modal" data-modal="#dvAdd-banner" data-target="{{ route('admin.banners.create_modal') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="banners-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Link</th>
                            <th>Image</th>
                            <th style="min-width: 65px;">Action</th>
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
@include('admin.pages.banners.add-modal')
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
            var dbTable = $('#banners-table');
            var pageModal = $('#dvAdd-banner');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.banners.data') }}",
                        columns: [
                            {data: 'link', name: 'link'},
                            {data: 'image', name: 'image'},
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

            pageModal.on('af.success','#add-banner-form',function() {
                $('#dvAdd-banner').fadeOut(350);
                $(this).trigger('hidden.bs.modal');
                pageModal.modal('hide');
                dbTableColumns.draw();
            });

            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-banner-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        link : {
                            url: true
                        },
                        image : {
                            required : true
                        }
                    },
                    messages: {
                        name: "Enter Banner Name"
                    }
                });


            });




        });
    </script>
@endpush
