@extends('layouts.affiliate')
@section('title','My Orders')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">My Orders</h3>
                    </div>

                    <div class="col-md-12">
                        <div class="panel panel-info">

                                <div id="sales_container" class="panel-body">
                                    @if($sales->count()==0)
                                        <p style="">No sales details found</p>
                                    @endif

                                @foreach($sales as $sale)
                                    <div id="dvsales_{{ $sale->id }}">
                                        <div id="salerow" class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
                                                <p><strong>{{ $sale->created_at->format('d F, Y') }}</strong></p>
                                                <hr style="margin: 8px 0px;">
                                                <p style="font-weight: bold;">Mode
                                                    @if($sale->pay_method == Utility::PAYMENT_ONLINE)
                                                        <small class="label label-primary">Online Payment</small>
                                                    @elseif($sale->pay_method == Utility::PAYMENT_OFFLINE)
                                                        <small class="label label-primary">Offline Payment</small>
                                                    @elseif($sale->pay_method == Utility::PAYMENT_COD)
                                                        <small class="label label-primary">Cash on Delivery</small>
                                                    @else

                                                    @endif
                                                </p>
                                                <p style="font-weight: bold;">Cash {!! $sale->is_paid ? '<small class="label bg-green">Paid</small>' : '<small class="label bg-red">Not Paid</small>' !!}</p>
                                                <p style="font-weight: bold;">Order Status <small class="label bg-blue">{{ Utility::saleStatus()[$sale->status] }}</small></p>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                                                <h4 class="product-name">
                                                    <small class="hidden-lg hidden-md hidden-sm">
                                                        {{ $sale->created_at->format('d F, Y') }}<br>
                                                        <hr style="margin: 8px 0px;">
                                                        <p style="font-weight: bold;">Mode
                                                            @if($sale->pay_method == Utility::PAYMENT_ONLINE)
                                                                <small class="label label-primary">Online Payment</small>
                                                            @elseif($sale->pay_method == Utility::PAYMENT_OFFLINE)
                                                                <small class="label label-primary">Offline Payment</small>
                                                            @elseif($sale->pay_method == Utility::PAYMENT_COD)
                                                                <small class="label label-primary">Cash on Delivery</small>
                                                            @else

                                                            @endif
                                                        </p>
                                                        <p style="font-weight: bold;">Cash {!! $sale->is_paid ? '<small class="label bg-green">Paid</small>' : '<small class="label bg-red">Not Paid</small>' !!}</p>
                                                        <p style="font-weight: bold;">Order Status <small class="label bg-blue">{{ Utility::saleStatus()[$sale->status] }}</small></p>
                                                    </small>

                                                    <strong>{{ $sale->order_no }}</strong><br>
                                                    @if(($sale->pay_method == Utility::PAYMENT_OFFLINE) && (!$sale->is_paid == 1))
                                                        <label class="utr_no_div">
                                                            <input type="text" name="utr_no" id="utr_no_{{ $sale->id }}" value="{{ $sale->utr_no }}" placeholder="Enter UTR No." style="width: 100%;" >
                                                            <small class="utr_no_submit" data-sale_id="{{ $sale->id }}">
                                                                <i style="cursor: pointer;" class="fa fa-upload"></i>
                                                            </small>
                                                        </label>
                                                            <p><small>Make the payment by transferring the amount to <a onclick="event.preventDefault()" id="bank_details" href="#">Kerala health Mart Bank account</a> and enter your payment referance number here.</small></p>
                                                    @endif
                                                    @if($sale->delivery_type && !$sale->is_paid)
                                                        <p><small class="text-danger">You must pay the Shipping charges at the time of delivery</small></p>
                                                    @endif
                                                </h4>
                                                <p>
                                                    <?php $sNo = 1; ?>
                                                    @foreach($sale->sale_details as $sale_details)
                                                            {{ $sNo }}. {{ $sale_details->product->name }} ({{ Utility::getCategoryName($sale_details->type_size) }})
                                                        <br><small>Quantity: {{ $sale_details->quantity }} {{ $sale_details->product->unit_om }}</small>

                                                        <br><br>

                                                        <?php $sNo++; ?>
                                                    @endforeach
                                                </p>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-9 text-right" style="padding-right: 0px;">
                                                    <h5>
                                                        Sub Total : <i class="fa fa-inr"></i>{{ $sale->sub_total }} <br><br>
                                                        Delivery Charge : {!! !empty($sale->delivery_charge) ? '<i class="fa fa-inr"></i>'.$sale->delivery_charge : '--' !!} <br><br>
                                                        <strong>Grand Total : <i class="fa fa-inr"></i>{{ $sale->sub_total + $sale->delivery_charge }} </strong></h5>
                                                </div>
                                                <div id="order_cancel" class="col-lg-4 col-md-4 col-sm-4 col-xs-3" style="padding-top: 7px;">
                                                    @if($sale->is_paid)
                                                        <p style="font-weight: bold; font-size:20px;"><a target="_blank" href="{{ route('affiliate.bill.download.specific',[$affiliate_slug, $sale->id,'download'=>'invoice']) }}" ><small class="label bg-green fa fa-download"> Download Bill</small></a></p>
                                                    @else
                                                        {{-- <p style="font-weight: bold; font-size:20px;"><a href="#" class="pay_bill" data-url="{{ route('affiliate.checkout.pay.later',[$affiliate_slug, $sale->id]) }}"><small class="label bg-green fa fa-inr"> Pay Bill</small></a></p> --}}
                                                    @endif
                                                    @if($sale->status == Utility::SALE_STATUS_NEW)
                                                            <p style="font-weight: bold; font-size:20px;"><a href="{{ route('affiliate.order.cancel',$affiliate_slug) }}" data-plugin="ajaxGetRequest" data-type="GET" data-formdata='[{"sale":"{{ $sale->id }}"}]' data-conf-message="Do you really want to cancel the order..?"><small class="label bg-red fa fa-remove"> Cancel Order</small></a></p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                @endforeach
                                {!! $sales->links() !!}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
        <!-- ALERTIFY -->
    <!-- JavaScript -->
    <script src="{{ asset('vendor/alertifyjs/alertify.js') }}"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/alertifyjs/css/alertify.min.css') }}">
    <!-- Default theme -->
    <link rel="stylesheet" href="{{ asset('vendor/alertifyjs/css/themes/bootstrap.min.css') }}"/>
    <!--// ALERTIFY //-->
<script>
    $(document).ready(function() {
        $('#order_cancel').on('ajax-get-request.success', function (e,data) {
            if(data.canceled==1) {
                $('#dvsales_' + data.sale_id).fadeOut();
            }else {
                $('#dvsales_' + data.sale_id).prepend('<p style="color: red;">You can\'t cancel this order</p>');
                $('#dvsales_' + data.sale_id).find('#salerow').append('<p style="color:green;font-weight: bold;">Status: Your Order is ' + data.status +'.</p>');
                $('#order_cancel').hide();
            }
            if(data.count==0) {
                $('#sales_container').empty().html('<p>No sales details found</p>')
            }
        });


        $('.utr_no_submit').click(function(e) {
            e.preventDefault();
            var sale_id = $(this).data('sale_id');
            var utr_no = $('#utr_no_'+sale_id).val();
            var url = "{{ route('affiliate.utr.update',$affiliate_slug) }}";
            var formdata = {utr_no: utr_no, sale_id: sale_id};
            $.ajax({
                type: 'POST',
                data: formdata,
                url: url,
                success: function (data) {
                    $('#msg-bg').fadeIn().html('<p class="text-success">UTR number updated successfully..!</p>');
                    $("#msg-bg").fadeOut( 2500, function() {
                        // Animation complete.
                    });
                }
            });

        });

        $(".pay_bill").click(function(e){
            e.preventDefault();
            var url = $(this).data('url');
            $.get(url, function(data){
                window.location.replace(data);
            });
        });

        var accnt_details = "<p><strong>Account Name : {{ Utility::settings('account_name') }}<br>" +
                "Account Number : {{ Utility::settings('account_number') }}<br>" +
                "Payee bank : {{ Utility::settings('bank_name') }}<br>" +
                "IFSC Code : {{ Utility::settings('ifsc_code') }}<br>" +
                "Branch : {{ Utility::settings('bank_branch') }}<br>" +
                "UPI ID : {{ Utility::settings('upi_id') }}<br>" +
                "GPay : {{ Utility::settings('google_pay') }}</strong></p><br>" ;
                $('[id^=bank_details]').tooltip("destroy").tooltip({title: accnt_details, html:true});

    });
</script>
@endpush
