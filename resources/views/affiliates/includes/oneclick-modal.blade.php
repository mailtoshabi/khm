<div class="modal fade" id="oneClickModal">
    <div class="modal-dialog ">
        <form class="login modal-content" id="oneclick_form" action="{{ route('affiliate.product.oneclick.purchase',$affiliate_slug) }}" method="POST" role="form" data-plugin="ajaxForm">
            <div class="modal-body row" >
                <div class="close_btn">
                    <button type="button" class="close" data-dismiss="modal">✕</button>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12" style="padding: 12px 6%;">
                    <p style="font-size: 17px; font-weight: bold;">Enter your mobile number</p>
                    <p style="font-size: 15px; font-weight: bold;">നിങ്ങളുടെ മൊബൈല്‍ നമ്പര്‍ നല്‍കുക.</p>
                </div>
                <div class="right-modal-content col-md-6 col-sm-12 col-xs-12" style="padding: 30px 2%;">
                    <div class="form-group @if ($errors->has('oneclick_phone')) has-error @endif">
                        <input type="hidden" name="product_id" value="{{ $product_id }}" />
                        <input type="text" name="phone" id="oneclick_phone" placeholder="Mobile Number" value="{{ Auth::guard('customer')->guest() ? '' : Auth::guard('customer')->user()->phone }}" />
                        @if ($errors->has('oneclick_phone'))
                            <span class="help-block">
                          {{ $errors->first('oneclick_phone') }}
                      </span>
                        @endif
                    </div>
                    <input type="submit" value="OK" class="btn btn-lg" />
                </div>
            </div>

        </form>
    </div>
</div>
@push('page_scripts')
<script>
    $(document).ready(function() {
        $('#oneClickModal').on('af.success','#oneclick_form',function(e,data) {
            $('#jq-loader').hide();
            $('#oneClickModal').modal('hide');
            var affiliate_name = "{{ $affiliate->user->name }}";
            var showData = '<p class="text-success">Thank you for choosing ' + affiliate_name + '. Your purchase order is confirmed. Our executive will contact you soon.<br> </p>';
            $('#msg-bg').show().html(showData);
            $("#msg-bg").fadeOut( 12000, function() {
                // Animation complete.
            });
        });

        jQuery.validator.addMethod("phoneno", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
        }, "Enter valid phone number");
        var $validator = $('#oneclick_form').validate({
            rules: {
                phone : {
                    required : true,
                    phoneno : true
                }
            },
            messages: {
                phone: {
                    required: "Phone is required"
                }
            }
        });


    });
</script>
@endpush
