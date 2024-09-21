@extends('layouts.affiliate')
@section('title','Payment Options')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">
        <div class="container">
            <div class="row">
                <div class="col-md-8 khm-cart khm-checkout">
                    <div class="panel panel-info inactive" style="margin-top: 10px;">
                        <div class="panel-heading" style="background: #fff;">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_checkout"><small class="checkout_no inactive">1</small> LOGIN OR SIGNUP</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-info inactive" style="margin-top: 10px;">
                        <div class="panel-heading" style="background: #fff;">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_checkout"><small class="checkout_no inactive">2</small> DELIVERY ADDRESS</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-info" style="margin-top: 10px;">
                        <div class="panel-heading" style="background: #2874f0;">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_checkout"><small class="checkout_no">3</small> PAYMENT OPTIONS</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="dvEmptycontainer01">
                            <div class="panel-body" id="dvpayment_option_form">
                                <form class="login" action="{{ route('affiliate.checkout.payment_options.store',$affiliate_slug) }}" method="post" id="payment_option_form" role="form" >
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-xs-12 col-md-9" style="padding: 0 0px; margin-left: 10px;">
                                            {{--<div class="col-md-12 payment_option" >
                                                <label>
                                                    <input type="radio" name="payment_option" value="{{ Utility::PAYMENT_COD }}" checked >
                                                    Cash on Delivery
                                                </label>
                                                <p id="dvcod" style="/*display: none;*/">Calicut & Malappuram Districts only</p>
                                            </div>--}}

                                            @if(empty($affiliate->bank_account) && empty($affiliate->upi_id) && empty($affiliate->g_pay))
                                            @else
                                            <div class="col-md-12 payment_option" >
                                                <label>
                                                    <input type="radio" name="payment_option" value="{{ Utility::PAYMENT_OFFLINE }}" >
                                                    Offline Payment
                                                </label>
                                                <div id="dvoffline" style="display: none;">
                                                    <p>Step 1 : Press <b>Continue</b> button and complete your purchase by selecting <i>offline payment</i></p>
                                                    <p>Step 2 : Deposit/transfer <strong>Amount Payable</strong> to {{ $affiliate->user->name }} account by using either of the following methods </p>
                                                    @if(!empty($affiliate->bank_account))
                                                    <p> >> To Bank account shown below.</p>
                                                    <div style="padding-left: 20px;color: #11549a; font-weight: bold;">
                                                        {!! $affiliate->bank_account !!}
                                                    </div>
                                                        <br>
                                                    @endif

                                                    @if(!empty($affiliate->upi_id))
                                                        <p> >> To UPI ID shown below.</p>
                                                        <div style="padding-left: 20px;color: #11549a; font-weight: bold;">
                                                            {{ $affiliate->upi_id }}
                                                        </div>
                                                        <br>
                                                    @endif

                                                    @if(!empty($affiliate->g_pay))
                                                        <p> >> To Google Pay Account shown below.</p>
                                                        <div style="padding-left: 20px;color: #11549a; font-weight: bold;">
                                                            {{ $affiliate->g_pay }}
                                                        </div>
                                                        <br>
                                                    @endif


                                                    <p>Step 3 : For every successful deposit/transfer, you will get UTR number from your Bank or system. Submit the UTR number in the <strong>next step</strong> OR <strong>My Account >> My order</strong> Page</p>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="footerNavWrap clearfix">
                                            </div>
                                        </div>

                                        <div class="col-xs-12 col-md-9 above_captcha" >

                                            <div class="form-group col-md-4 col-xs-6 pull-right" style="padding-right: 0px;">
                                                <button id="payment_option_btn" type="submit" class="place_order btn btn-lg ladda-button" data-style="zoom-out" style="color: #fff; font-weight: bold" ><span class="ladda-label">CHECK OUT</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

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

        $(".btn-refresh").click(function() {
            /*refreshCaptcha();*/
        });

            refreshCart();

        /*$('#dvEmptycontainer01').on('af.success','#payment_option_form',function(e,data) {
                window.location.replace(data);
        }).on('af.error','#payment_option_form',function(e,data) {
            //refreshCaptcha();
        });*/

        $(document).on('click','input[name="payment_option"]', function () {
            if($(this).val() == '{{ Utility::PAYMENT_ONLINE }}') {
                $('#dvonline').fadeIn();
                $('#dvoffline').fadeOut();
                $('#dvcod').fadeOut();
            }
            else if($(this).val() == '{{ Utility::PAYMENT_OFFLINE }}') {
                $('#dvonline').fadeOut();
                $('#dvoffline').fadeIn();
                $('#dvcod').fadeOut();
            }
            else if($(this).val() == '{{ Utility::PAYMENT_COD }}') {
                $('#dvonline').fadeOut();
                $('#dvoffline').fadeOut();
                $('#dvcod').fadeIn();
            }
            else {

            }

            if($(this).val() != '{{ Utility::PAYMENT_ONLINE }}') {
                $('#dvrecaptcha').fadeIn();
                $('#payment_option_btn').text('CONTINUE');
            }else {
                $('#dvrecaptcha').fadeOut();
                $('#payment_option_btn').text('CHECKOUT');
            }
        });

        $(document).on('click','#payment_option_btn', function (e) {
            e.preventDefault();

            $('form#payment_option_form').submit();
            /*$('#dvpayment_option_form').find('form[data-plugin="ajaxForm"]').trigger("submit");*/

        });

        var $validator = $('#payment_option_form').validate({
            rules: {
                payment_options: {
                    required: true
                }
            },
            messages: {
                payment_options: {
                    required: "Select any of the option"
                }
            }
        });
    });

    /*function refreshCaptcha() {
        $.ajax({
            type: 'GET',
            url: "", {{--{{ route('refresh_captcha') }}--}}
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    }*/
</script>
{{--<script src='https://www.google.com/recaptcha/api.js'></script>--}}
@endpush
