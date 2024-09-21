@extends('layouts.affiliate')
@section('title','My Cart')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">
        <div class="container">
            <div class="row">
                <div class="col-md-8 khm-cart">
                    <div class="panel panel-info" style="margin-top: 10px;">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_cart">My Cart <span class="dvNonEmptycontainer">(<span id="total_quantity_my_cart">{{ Cart::getTotalQuantity() }}</span>)</span>@if(!Cart::isEmpty()) <small><a id="empty_cart" href="">Empty Cart</a></small> @endif</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="dvEmptycontainer01" class="hidden">
                            <div class="panel-body">
                                <div class="row col-md-12">
                                    <p>Your cart is empty</p>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="row">
                                    <div class="col-xs-12 col-md-12">
                                        <button class="place_order btn btn-default" type="button" onclick="goBack()"><span class="fa fa-angle-left"></span> &nbsp;&nbsp;SHOPE NOW</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Cart::isEmpty())
                            <div >
                                <div class="panel-body">
                                    <div class="row col-md-12">
                                        <p>Your cart is empty</p>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12">
                                            <button class="place_order btn btn-default" type="button" onclick="goBack()" ><span class="fa fa-angle-left"></span> &nbsp;&nbsp;SHOPE NOW</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="dvNonEmptycontainer">
                                <div class="panel-body">
                                    @foreach($cartCollection as $index => $cartitem)
                                        <div class="{{ (Utility::get_stock($cartitem->id,$cartitem->attributes->type) == 0) || (Utility::get_stock($cartitem->id,$cartitem->attributes->type) < $cartitem->quantity) ? 'bg-danger' : '' }}" id="prod_container_{{ $index }}" style="padding: 10px;">
                                            <div class="row">
                                                <div class="col-xs-6 col-md-2"><img class="img-responsive" src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $cartitem->attributes->image) }}">
                                                </div>
                                                <div class="col-xs-6 col-md-4">
                                                    @if((Utility::get_stock($cartitem->id,$cartitem->attributes->type) == 0) || (Utility::get_stock($cartitem->id,$cartitem->attributes->type) < $cartitem->quantity))
                                                        <p id="outofstock_{{ $index }}" class="text-danger"><i class="fa fa-warning"></i><small> This item seems to be out of stock or less in stock. Kindly check quantity before proceeding</small></p>
                                                        <p id="totalstock_{{ $index }}" class="text-info"><small><i class="fa fa-info-circle" ></i> Total stock is </small>{{ Utility::get_stock($cartitem->id,$cartitem->attributes->type) }}</p>
                                                    @else
                                                        <p id="outofstock_{{ $index }}" class="text-danger"></p>
                                                    @endif
                                                    <p class="product-name"><a target="_blank" href="{{ route('affiliate.all.slug', [$affiliate_slug, Utility::getProductSlug($cartitem->id)]) }}"> {{ $cartitem->name }}</a><br>
                                                    <small style="color:#2874f0;">{{ Utility::getCategoryName($cartitem->attributes->type) }}</small></p>
                                                    <p><small>{{ strip_tags(Utility::productShortDescription($cartitem->id)) }}</small></p>
                                                </div>
                                                <div class="col-xs-12 col-md-6">
                                                    <div class="col-xs-5 text-right">
                                                        <h6><strong><i class="fa fa-inr"></i><span id="cartItemPrice_{{ $index }}">{{ round($cartitem->price,2) }}</span> <span class="text-muted">x</span></strong></h6>
                                                    </div>
                                                    <div class="col-xs-3">
                                                        <input id="prod_quanity_{{ $index }}" type="text" class="form-control input-sm" value="{{ $cartitem->quantity }}">
                                                    </div>
                                                    <div class="col-xs-2">
                                                        <button type="button" class="btn btn-link btn-xs update_cart" data-index="{{ $index }}" data-action="{{ route('affiliate.product.update_cart',[$affiliate_slug,$index]) }}">
                                                            <span class="glyphicon glyphicon-refresh text-primary"> </span>
                                                        </button>
                                                    </div>
                                                    <div class="col-xs-2">
                                                        <button type="button" class="btn btn-link btn-xs delete_cart" data-index="{{ $index }}" data-action="{{ route('affiliate.product.delete_cart',[$affiliate_slug,$index]) }}">
                                                            <span class="glyphicon glyphicon-trash text-primary"> </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        </div>
                                    @endforeach


                                    <div class="row">
                                        <div class="text-center">
                                            <div class="col-xs-6 col-md-9">
                                                <h6 class="text-right">Added new items?</h6>
                                            </div>
                                            <div class="col-xs-6 col-md-3">
                                                <button type="button" class="btn btn-default btn-sm btn-block" onclick="refreshPage()">
                                                    Refresh cart
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-9">
                                            <button class="back-to-product btn btn-default" type="button" onclick="location.href='{{ route('affiliate.category.all',$affiliate_slug) }}'"><span class="fa fa-angle-left"></span> &nbsp;&nbsp;CONTINUE SHOPPING</button>
                                        </div>
                                        <div class="col-xs-12 col-md-3">
                                            <button type="button" class="place_order btn btn-default ladda-button" data-style="zoom-out" style="color: #fff; font-weight: bold" onclick="location.href='{{ Auth::guard('customer')->guest() ? route('affiliate.checkout.login',$affiliate_slug) : route('affiliate.checkout.address',$affiliate_slug) }}'" >
                                                <span class="ladda-label">PLACE ORDER &nbsp;&nbsp;<span class="fa fa-angle-right"></span></span>
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endif
                    </div>
                </div>
                @if(Cart::isEmpty())
                @else
                    @include('affiliates.includes.price-detail-sidebar')
                @endif
            </div>
        </div>
    </div>
    <!-- Wrapper -->
@endsection
@push('page_scripts')
<script>
    $(document).ready(function() {

        refreshCart();

        $("#empty_cart").click(function(e){
            e.preventDefault();
            var url = "{{ route('affiliate.product.cart.clear',$affiliate_slug) }}";
            $.get(url, function(data){
                if(data==1) {
                    refreshCart();
                    $('#dvEmptycontainer01').removeClass('hidden');
                    $('.dvNonEmptycontainer').remove();
                }

            });
        });

        $(".update_cart").click(function(e){
            e.preventDefault();
            var index = $(this).data('index');
            var prod_container = $('#prod_container_'+index);
            var prod_quantity = parseInt($('#prod_quanity_'+index).val());
            var formdata = {quantity: prod_quantity};
            var url = $(this).data('action');
            $.ajax({
                type: "GET",
                url: url,
                data: formdata,
                success: function (data) {

                    /*$('#mycart-total-quantity').text(data.cart_total);*/
                    var priceContainer = $('#cartItemPrice_'+index);
                    var prodQuantityContainer = $('#prod_quanity_'+index);
                    var outofstock = $('#outofstock_'+index);
                    var totalstock = $('#totalstock_'+index);
                    priceContainer.text(parseFloat(data.price).toFixed(2));
                    prodQuantityContainer.val(data.quantity);

                    if(data.updated == 1) {
                        outofstock.html('');
                        totalstock.html('');
                        prod_container.removeClass('bg-danger');
                        $('#msg-bg').show().html('<p class="text-success">Cart updated successfully..!</p>');
                        $("#msg-bg").fadeOut( 2500, function() {
                            // Animation complete.
                        });
                    }
                    else {
                        /*outofstock.html('<i class="fa fa-warning"></i><small> The quantity you are trying is more than the stock.</small> <small class="text-info" ><i class="fa fa-info-circle" ></i> Total stock is ' + data.stock + '</small>');*/
                        $('#msg-bg').show().html('<p class="text-danger">Out of stock or Less in stock..!</p>');
                        $("#msg-bg").fadeOut( 2500, function() {
                            // Animation complete.
                        });
                    }
                    refreshCart();
                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });

        });

        $(".delete_cart").click(function(e){
            e.preventDefault();

            var index = $(this).data('index');
            var prod_container = $('#prod_container_'+index);
            var url = $(this).data('action');
            $.get(url, function(data){
                prod_container.fadeOut(300, function () {
                    $(this).remove();
                });
                refreshCart();
                $('#msg-bg').show().html('<p class="text-danger">Item removed successfully..!</p>');
                $("#msg-bg").fadeOut( 2500, function() {
                    // Animation complete.
                });
                if(data==1) {
                    $('#dvEmptycontainer').show();
                    $('.dvNonEmptycontainer').hide();
                }
            });

        });

    });
</script>

@endpush
