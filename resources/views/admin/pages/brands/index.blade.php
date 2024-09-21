    @extends('admin.layouts.list')
@section('title','Brands')
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Brands
        <small>List of All Product Brands</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        {{--<li><a href="#">Tables</a></li>--}}
        <li class="active">Brands</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">

            <div class="box">
                <div class="box-header">
                    <a href="#" data-plugin="render-modal" data-modal="#dvAdd-brand" data-target="{{ route('admin.brands.create_modal') }}" class="btn btn-primary text-capitalize pull-right">Create New</a>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table id="brands-table" class="table table-bordered table-striped ">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Email</th>
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
@include('admin.pages.brands.add-modal')
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
            var dbTable = $('#brands-table');
            var pageModal = $('#dvAdd-brand');
            var dbTableColumns =
                    dbTable.DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: "{{ route('admin.brands.data') }}",
                        columns: [
                            {data: 'name', name: 'name'},

                            {
                                data: 'image',
                                name: 'image',
                                orderable: false,
                                searchable: false
                            },
                            {data: 'email', name: 'email'},
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

            pageModal.on('af.success','#add-brand-form',function() {
                $('#dvAdd-brand').fadeOut(350);
                $(this).trigger('hidden.bs.modal');
                pageModal.modal('hide');
                dbTableColumns.draw();
            });






            /*dbTable.on('data-popup.edit',function(e,data) {
                e.preventDefault();
                var url = data.url;
                var update_url = data.update_url;
                $.get(url, function (data) {
                    $('#pop-title').text('Edit');
                    pageForm.attr('action',update_url);
                    var actionMethod = '<input type="hidden" id="action_method" name="_method" value="PUT" />';
                    pageForm.prepend(actionMethod);
                    $('#name').val(data.brand.name);
                    $("#parent option[value='" + data.brand.id + "']").remove();
                    if(data.brand.parent !=0) {
                        $('#parent').val(data.brand.parent).trigger('change');
                    }
                    $('#btn-brand-add').text('Update');
                    /!*$('#dvAdd-brand').modal('show');*!/
                });
            });*/

            /*pageModal.on('hidden.bs.modal', function (e) {
                pageForm.attr('action',pageFormAction);
                pageForm.find('#action_method').remove();
                $('#pop-title').text('Add New');
                var option = new Option($('#name').val(), ''); $('#parent').append($(option));
                $('#name').val('');
                $('#parent').val('').trigger('change');
                $('#btn-brand-add').text('Create');
            });*/

            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-brand-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        name : {
                            required : true
                        },
                        slug : {
                            required : true
                        }
                    },
                    messages: {
                        name: "Enter Brand Name",
                        slug: "Enter URL Slug"
                    }
                });


            });




        });
    </script>
@endpush
