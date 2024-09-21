<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-courier-details-form" action="{{ route('admin.sales.courier.update',[$sale->id]) }}" method="POST" role="form" data-plugin="ajaxForm">
            @if(isset($sale->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ !empty($sale->courier) ? 'Edit' : 'Add' }}</span> Courier Details</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="courier">Courier<span class="text-danger">*</span></label>
                        <select class="form-control select2" id="courier" name="courier" style="width: 100%;" >
                            @foreach(Utility::courier() as $index => $courier)
                                <option value="{{ $index }}" {{ $sale->courier == $index ? 'selected' : '' }} >{{ $courier['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="courier_track">Track Code<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="courier_track" name="courier_track" placeholder="Enter Track Code" value="{{ isset($sale->courier_track) ? $sale->courier_track : '' }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="delivery">Delivery Time<span class="text-danger">*</span></label>
                        <div class=""> {{--input-group--}}
                            <div class="row">
                                <div class="col-md-9 col-sm-9 col-lg-9 col-xs-9" style="padding-right: 0px">
                                    <input type="text" class="form-control" id="delivery" name="delivery" placeholder="Enter Delivery Time" value="{{ isset($sale->delivery) ? $sale->delivery : '' }}">
                                </div>
                                <div class="col-md-3 col-sm-3 col-lg-3 col-xs-3" style="padding-left: 0px">
                                    <input type="text" class="form-control" id="post_delivery" name="post_delivery" value="" placeholder="Days" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-12">
                        <input type="checkbox" class="" id="is_couriersms" name="is_couriersms" value="1" checked>
                        &nbsp;&nbsp;&nbsp;<label for="is_couriersms">Sent SMS</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-category-add">{{ !empty($sale->courier) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>