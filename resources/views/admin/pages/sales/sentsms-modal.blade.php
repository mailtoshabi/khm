<div class="modal fade" id="dvAdd-sms-details"  role="dialog" aria-labelledby="add-sms-details">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="sent-sms-details-form" action="{{ route('admin.sales.sms.sent',[$sale->id]) }}" method="POST" role="form" data-plugin="ajaxForm">
                @if(isset($sale->id))
                    <input type="hidden" name="_method" value="PUT" />
                @endif
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span id="pop-title"></span> SMS Details</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="courier">Enter SMS content<span class="text-danger">*</span></label><br>
                            <textarea style="width: 100%;" id="sms_content" name="sms_content" placeholder="SMS Content" >{{ $sale->sms_content }}</textarea>
                        </div>



                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="btn-category-add">Sent SMS</button>
                </div>
            </form>
        </div>
    </div>
</div>