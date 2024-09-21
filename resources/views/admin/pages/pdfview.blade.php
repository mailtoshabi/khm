<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $sale->order_no }}</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container"> <!--container-->
    <div class="row">
        <div role="tabpanel">

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-md-12">
                        <table width="100%" style="border: none;" border="0">
                            <tbody>

                            <tr>
                                <td width="50%" class="text-left">
                                    <p>Order through:</p>
                                    <img src="{{ asset('images/logo_footer.png') }}" width="160" alt="kerala health mart" style="" />
                                    <h4 style="margin: 5px 0px;">{{ config('app.domain') }}</h4>
                                    {!! !empty(Utility::settings('admin_email')) ? 'Email: '. Utility::settings('admin_email') : '' !!}
                                    {!! !empty(Utility::settings('admin_phone')) ? '<br>Contact No: '. Utility::settings('admin_phone') : '' !!}
                                </td>
                                <td width="50%" class="text-right">
                                    DC No: {{ $sale->order_no }}<br>
                                    Date : {{ $sale->created_at->format('d-m-Y') }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 text-center">
                        <p><strong>Delivery Challan</strong></p>
                    </div>
                    <div class="col-md-12">
                        <table width="100%" class="table table-hover table-bordered">
                            <tbody>
                            <tr>
                                <td width="50%" class="text-left">
                                    <strong>Sold by:</strong>
                                    @if($sale->user->id == Utility::KHM_USER_ID)
                                        <h4>Kerala healthmart Pvt Ltd</h4>
                                        {{-- <h5><b>Surgicals Chemicals & Diagnostics Distributor</b></h5> --}}
                                        <h5>60/5690, Popular building</h5>
                                        <h5>Mooriyad road, PUTHIYA PALAM. Calicut Dt, Kerala - 673002</h5>
                                        <h5>GST No : 32AAICK6482F1ZH</h5>
                                    @else
                                        <h4>{{ $sale->user->name }}</h4>
                                        <h5>{{ $sale->user->affiliate->location  }} {{ $sale->user->affiliate->city  }} - {{ !empty($sale->user->affiliate->city) ? $sale->user->affiliate->city . ' Dt.'  : ''  }} {{ $sale->user->affiliate->pin }}</h5>
                                        <h5>{{ $sale->user->affiliate->contact_email }}</h5>
                                        <h5>{{ $sale->user->affiliate->contact_phone }}</h5>
                                    @endif
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
                        <table width="100%" class="table table-hover table-bordered">
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

                <div class="row">
                    <div class="col-md-12">
                        <p>This is a computer generated DC</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
