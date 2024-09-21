<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-type-size-form" action="@if(isset($type->id)) {{ route('admin.products.types.update',[$type->id]) }} @else {{ route('admin.products.types.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" >
            @if(isset($type->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($type->id) ? 'Edit' : 'Add New' }}</span> Type & Size</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Type Size Name" value="{{ isset($type->name) ? $type->name : '' }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-category-add">{{ isset($type->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>