<div class="dvNonEmptycontainer">
    <div class="col-md-4 col-xs-12 col-sm-12 price_fix" style="margin-top: 10px;">
        <div class="cart_side">
            <h5>PRICE DETAILS</h5>
            <div class="col-xs-12">
                <div class="col-xs-6">Price (<span id="total_quantity">{{ Cart::getTotalQuantity() }}</span> Item)</div>
                <div class="col-xs-6 text-right"><i class="fa fa-inr"></i><span id="total_amount">{{ round($grandTotal,2) }}</span></div>
            </div>
            <div id="dvPackaging" class="col-xs-12" @if(session()->has('kerala_h_m_ship_option')) style="display: none;" @endif>
                <div class="col-xs-6">Packaging & Delivery Charges</div>
                <div class="col-xs-6 text-right text-success"><strong><span id="delivery_charge"></span></strong></div>
                <div style="display:none" id="delivery_note" class="col-xs-12"><span style="color:#fb641b; font-size: 15px;">*Free delivery for purchase above {{ Utility::settings('minimum_to_delivery_charge') }} rupees</span></div>
            </div>
            <div class="col-xs-12">
                <div style="display:none;" id="ship_note" class="col-xs-12"><span style="color:#fb641b; font-size: 15px;">* You should pay the shipping charges at the time of delivery.</span></div>
            </div>
            <div class="col-xs-12">
                <h5>SHIPPING OPTIONS</h5>
                <div style="padding-left: 20px;" class="col-xs-12">
                    <label><input type="radio" name="shipping_option" id="shipping_option_paid" data-url="{{ route('shipping.options',0) }}" value="{{ Utility::DELIVERY_TYPE_PAID }}" {{ session()->has('kerala_h_m_ship_option')? '' : 'checked' }} > Paid</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <label><input type="radio" name="shipping_option" id="shipping_option_topay" data-url="{{ route('shipping.options',1) }}" value="{{ Utility::DELIVERY_TYPE_TOPAY }}" {{ session()->has('kerala_h_m_ship_option')? 'checked' : '' }} > To pay</label>
                </div>
            </div>
            <div class="divder"></div>
            <div class="col-xs-12 amount_payable">
                <div class="col-xs-6">Amount Payable</div>
                <div class="col-xs-6 text-right"><i class="fa fa-inr"></i><span id="amount_payable"></span></div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </div>
</div>

@push('page_scripts')
<script>
    $(document).ready(function() {
        $("input[name='shipping_option']").click(function() {
            var $this = $(this);
            var url = $this.data('url');
            $.ajax({
                type: "GET",
                url: url,
                success: function (data) {
                    refreshCart();
                }
            });
        });

    });
</script>
@endpush
