@extends('layouts.affiliate')
@section('title','Success - your order is confirmed') {{--$product->name--}}
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!--container-->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h1 class="page_title"></h1>
                    </div>

                    <div class="col-sm-12">
                        <div>
                            <center>
                                @if($sale->pay_method == Utility::PAYMENT_ONLINE)
                                @if($sale->is_paid)
                                        <h3 class="text-success">{{ $message }}</h3>
                                    @else
                                        <h3 class="text-danger">Failed - your order is on pending as we have not received your payment. Please contact us if you have any queries.</h3>
                                    @endif
                                    <h5>Order number: #{{ $sale->order_no }}</h5>
                                @endif

                                @if($sale->pay_method == Utility::PAYMENT_OFFLINE)
                                <h3 class="text-success">{{ $message }}</h3>
                                <h5>Order number: #{{ $sale->order_no }}</h5>

                                    <p class="text-success"><strong>Transfer INR {{ $sale->sub_total + $sale->delivery_charge }} by using either of the following methods and Update your UTR Number in <a target="_blank" href="{{ route('myorders') }}">my order</a> page</strong></p>
                                    {{-- @if(!empty($affiliate->bank_account))
                                        <p> >> To Bank account shown below.</p>
                                        <div style="padding-left: 20px;color: #11549a; font-weight: bold;">
                                            {!! $affiliate->bank_account !!}
                                        </div>
                                        <br>
                                    @endif --}}

                                    <p><strong>
                                        Account Name : {{ Utility::settings('account_name') }}<br>
                                        Account Number : {{ Utility::settings('account_number') }}<br>
                                        Payee bank : {{ Utility::settings('bank_name') }}<br>
                                        IFSC Code : {{ Utility::settings('ifsc_code') }} <br>
                                        Branch : {{ Utility::settings('bank_branch') }}</strong><br>
                                    </strong></p>
                                    <br>

                                    {{-- @if(!empty($affiliate->upi_id))
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
                                    @endif --}}

                                    <p> >> To UPI ID shown below.</p>
                                    <div style="padding-left: 20px; color: darkblue; font-weight: bold;">
                                        {{ Utility::settings('upi_id') }}
                                    </div>
                                    <br>
                                    <p> >> To Google Pay Account shown below.</p>
                                    <div style="padding-left: 20px; color: darkblue; font-weight: bold;">
                                        {{ Utility::settings('google_pay') }}
                                    </div>
                                    <br>
                                    <label class="utr_no_div">
                                        <input type="text" name="utr_no" id="utr_no" value="{{ $sale->utr_no }}" placeholder="Enter UTR No." style="width: 100%;" >
                                        <input type="hidden" name="sale_id" id="sale_id" value="{{ $sale->id }}" >
                                    </label>
                                    <button type="button" class="btn btn-primary" id="submit_utr">Submit</button>
                                @endif
                                @if($sale->is_paid)
                                    {{-- <h5><i class="fa fa-download text-primary"></i> <a class="text-primary" target="_blank" href="{{ route('bill.download',['download'=>'invoice']) }}">Download Bill</a></h5> --}}
                                    <h5><i class="fa fa-download text-primary"></i> <a class="text-primary" target="_blank" href="{{ route('affiliate.bill.download.specific',[$affiliate_slug, $sale->id,'download'=>'invoice']) }}">Download Bill</a></h5>
                                @endif
                                <hr />
                            </center>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <address>
                                        <strong>Order through:</strong>
                                        <h3>{{ config('app.domain') . '/' . $affiliate_slug }}</h3>

                                    </address>

                                </div>

                                <div class="col-xs-6 text-right">
                                    <address>
                                        DC No: {{ $sale->order_no }}<br>
                                        Date : {{ $sale->created_at->format('d-m-Y') }}
                                    </address>

                                </div>


                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Delivery Challan</strong></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="text-left">
                                                        <strong>Sold by:</strong><br>
                                                        <h4>Kerala healthmart Pvt Ltd</h4>
                                                        {{-- <h5><b>Surgicals Chemicals & Diagnostics Distributor</b></h5> --}}
                                                        <h5>60/5690, Popular building</h5>
                                                        <h5>Mooriyad road, PUTHIYA PALAM. Calicut Dt, Kerala - 673002</h5>
                                                        <h5>GST No : 32AAICK6482F1ZH</h5>
                                                    </td>
                                                    <td width="50%" class="text-left">
                                                        <strong>Delivery Address:</strong><br>
                                                        {{ $customerDetails->name }}<br>
                                                        {!! !empty($customerDetails->customer->email) ? 'Email: '. $customerDetails->customer->email . '<br>' : '' !!}
                                                        {!! !empty($customerDetails->customer->phone) ? 'Mob: '. $customerDetails->customer->phone . '<br>' : '' !!}
                                                        {!! !empty($sale->address) ? $sale->address['address'] . '<br>' : '' !!}
                                                        {{ !empty($sale->address) ? $sale->address['place'].', ' : '' }} {{ !empty($sale->address) ? $sale->address['city'].', ' : '' }} {{ !empty($sale->address) ? $sale->address['pincode'].', ' : '' }}<br>
                                                        {{ !empty($sale->address) ? Utility::district_name($sale->address['district']) .', ' : '' }} {{ !empty($sale->address) ? Utility::state_name($sale->address['state']) .', ' : '' }} India.
                                                        {!! !empty($customerDetails->gstin) ? '<br>GSTIN : ' . $customerDetails->gstin : '' !!}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        {{-- <table class="table table-hover table-bordered">
                                            <tbody>
                                                <tr>
                                                    <td width="50%" class="text-left">
                                                        <strong>Sold by:</strong><br>
                                                        <h3>{{ $affiliate->user->name }}</h3>
                                                        <h5><b>{{ $affiliate->location }}</b></h5>
                                                        <h5>{{ $affiliate->city }}</h5>
                                                        <h5>{{ $affiliate->district }} - {{ $affiliate->pin }}</h5>
                                                        <h5>{{ $affiliate->contact_email }}</h5>
                                                        <h5>{{ $affiliate->contact_phone }}</h5>
                                                    </td>
                                                    <td width="50%" class="text-left">
                                                        <strong>Delivery Address:</strong><br>
                                                        {{ $customerDetails->name }}<br>
                                                        {!! !empty($customerDetails->customer->email) ? 'Email: '. $customerDetails->customer->email . '<br>' : '' !!}
                                                        {!! !empty($customerDetails->customer->phone) ? 'Mob: '. $customerDetails->customer->phone . '<br>' : '' !!}
                                                        {!! !empty($sale->address) ? $sale->address['address'] . '<br>' : '' !!}
                                                        {{ !empty($sale->address) ? $sale->address['place'].', ' : '' }} {{ !empty($sale->address) ? $sale->address['city'].', ' : '' }} {{ !empty($sale->address) ? $sale->address['pincode'].', ' : '' }}<br>
                                                        {{ !empty($sale->address) ? Utility::district_name($sale->address['district']) .', ' : '' }} {{ !empty($sale->address) ? Utility::state_name($sale->address['state']) .', ' : '' }} India.
                                                        {!! !empty($customerDetails->gstin) ? '<br>GSTIN : ' . $customerDetails->gstin : '' !!}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table> --}}
                                        <table width="100%" class="table table-hover">
                                            <thead>
                                            <tr>
                                                <td>No.</td>
                                                <td>Description of Goods</td>
                                                <td>HSN Code</td>
                                                <td class="text-center">Quantity</td>
                                                <td class="text-center">UOM</td>
                                                <td class="text-center">Rate</td>
                                                @if($sale->address['state']==Utility::STATE_ID_KERALA)
                                                    <td class="text-center">SGST</td>
                                                    <td class="text-center">CGST</td>
                                                @else
                                                    <td class="text-center">IGST</td>
                                                @endif
                                                <td class="text-center">Total</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $sNo = 1; ?>
                                            @foreach($sale->sale_details as $sale_details)
                                                <tr>
                                                    <td class="col-md-1">{{ $sNo }}</td>
                                                    <td class="col-md-4">{{ $sale_details->product->name }} ({{ Utility::getCategoryName($sale_details->type_size) }})</td>
                                                    <td class="col-md-1 text-center">{{ $sale_details->product->hsn_code }}</td>
                                                    <td class="col-md-1 text-center" style="text-align: center"> {{ $sale_details->quantity }} </td>
                                                    <td class="col-md-1 text-center" style="text-align: center"> {{ $sale_details->product->unit_om }} </td>
                                                    <td class="col-md-1 text-center">{{ Utility::CURRENCY_CODE }} {{ $sale_details->price }}</td>
                                                    @if($sale->address['state']==Utility::STATE_ID_KERALA)
                                                        <td class="col-md-1 text-center">{{ $sale_details->product->sgst() }}%</td>
                                                        <td class="col-md-1 text-center">{{ $sale_details->product->cgst() }}%</td>
                                                    @else
                                                        <td class="col-md-1 text-center">{{ $sale_details->product->tax }}%</td>
                                                    @endif

                                                    <td class="col-md-1 text-center">{{ Utility::CURRENCY_CODE }} {{ $sale_details->price * $sale_details->quantity }}</td>
                                                </tr>
                                                <?php $sNo++; ?>
                                            @endforeach

                                            <tr>
                                                <td colspan="{{ $sale->address['state']==Utility::STATE_ID_KERALA ? '4' : '3' }}">
                                                    <p style="font-size: 10px;">
                                                        Declaration: This delivery challan is issued in adjustable to original invoice
                                                    </p>
                                                </td>
                                                <td colspan="2" class="text-right">
                                                    <p>
                                                        <strong>Subtotal: </strong>
                                                    </p>
                                                    <p>
                                                        <strong>Shipping Charge : </strong>
                                                    </p>
                                                </td>
                                                <td colspan="3" class="text-center">
                                                    <p>
                                                        <strong>{{ Utility::CURRENCY_CODE }} {{ $sale->sub_total }}</strong>
                                                    </p>
                                                    <p>
                                                        <strong>{!! $sale->delivery_charge==0 ? '-' : Utility::CURRENCY_CODE . ' '.$sale->delivery_charge !!}</strong>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="{{ $sale->address['state']==Utility::STATE_ID_KERALA ? '4' : '3' }}">
                                                    <p style="text-transform:uppercase;"> {{ Utility::currencyToWords($sale->sub_total + $sale->delivery_charge) }}</p>
                                                </td>
                                                <td colspan="2" class="text-right"><h4><strong>Grand Total: </strong></h4></td>
                                                <td colspan="3" class="text-center text-primary"><h4><strong>{{ Utility::CURRENCY_CODE }} {{ $sale->sub_total + $sale->delivery_charge }}</strong></h4></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p>This is a computer generated DC</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
<script>
    $(document).ready(function() {

    });
</script>
@endpush
