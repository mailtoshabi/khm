<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-pin-form" action="@if(isset($pin->id)) {{ route('admin.stores.pins.update',[$pin->id]) }} @else {{ route('admin.stores.pins.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" enctype="multipart/form-data">
            @if(isset($pin->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($pin->id) ? 'Edit' : 'Add New' }}</span> Pin</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Pin Number" value="{{ isset($pin->name) ? $pin->name : '' }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-pin-add">{{ isset($pin->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>