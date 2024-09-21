@extends('admin.layouts.list')
@section('title','Order number: #'. $sale->order_no)
@section('content')

        <!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Sale
        <small>View</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.index') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ route('admin.sales.index') }}">Sales</a></li>
        <li class="active">Sale detail</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div role="tabpanel">

            <div class="row">
                <div class="col-sm-12" style="margin: 0 10px;">
                    <h1 class="page_title"></h1>
                </div>
                <div class="col-sm-12">
                    <div>
                        <center>
                            <h4>Order number: #{{ $sale->order_no }}</h4>
                            {{--<h5></h5>--}}
                            <strong>Customer Details</strong><br>
                            <a href="#">{{ $sale->customer->customer_detail->name }}</a><br>
                            <i class="fa fa-envelope" style="color:#666"></i> {!! $sale->customer->email . '<br>' !!}
                            <i class="fa fa-phone" style="color:#666"></i> {!! $sale->customer->phone . '<br>' !!}
                            {{--<h5><i class="fa fa-download text-primary"></i> <a class="text-primary" target="_blank" href="{{ route('admin.sales.bill.download',[$sale->id,'download'=>'invoice']) }}">Download Bill</a></h5>
                            <h3>Order Through <a href="{{ $sale->user->id == Utility::KHM_USER_ID ? '#' : route('admin.affiliates.edit',$sale->user->id) }}" target="{{ $sale->user->id == Utility::KHM_USER_ID ? '_self' : '_blank' }}" style="{{ $sale->user->id == Utility::KHM_USER_ID ? '' : 'color: red' }}">{{ $sale->user->name }}</a> {{ $sale->user->id == Utility::KHM_USER_ID ? ' (WEB ADMIN' : '' }}</h3>--}}
                            <hr />
                        </center>
                    </div>
                </div>
            </div>

            {{--<div class="row">
                <div class="col-xs-12">

                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Courier Service Details</h3>
                            <a href="#" data-plugin="render-modal" data-modal="#dvAdd-courier-details" data-target="{{ route('admin.sales.courier.edit',[$sale->id]) }}" class="btn btn-primary text-capitalize pull-right" id="courier-add-buton">{{ !empty($sale->courier) ? 'Edit' : 'Add' }} Details</a>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="products-table" class="table table-bordered table-striped ">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Website</th>
                                    <th>Track Code</th>
                                    <th>Delivery Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td id="tbl_courier_name">{{ !empty($sale->courier) ? Utility::courier()[$sale->courier]['name'] : '--' }}</td>
                                    <td id="tbl_courier_web">{{ !empty($sale->courier) ? Utility::courier()[$sale->courier]['website'] : '--' }}</td>
                                    <td id="tbl_courier_code">{{ !empty($sale->courier_track) ? $sale->courier_track : '--' }}</td>
                                    <td id="tbl_delivery">{{ !empty($sale->delivery) ? $sale->delivery . ' Days' : '--' }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>--}} {{--courier service details--}}
            <!-- /.row -->

            <div class="">
                {{--@if($sale->pay_method == Utility::PAYMENT_OFFLINE)
                    <div class="col-sm-2">
                        UTR No.
                    </div>
                    <div class="col-sm-2">
                        <input type="text" name="utr_no" id="utr_no" value="{{ $sale->utr_no }}" style="width:100%; position: relative;"> <i id="utr_no_submit"  class="fa fa-upload" style="position: absolute; right: 22px; bottom: 5px; cursor: pointer"></i>
                    </div>

                @else
                    <div class="col-sm-4">

                    </div>
                @endif--}}
                {{--<div class="col-sm-4 ">
                    <a href="#" data-plugin="render-modal" data-modal="#dvAdd-sms-details"  class="btn btn-primary text-capitalize pull-right" id="sms-add-buton">Sent SMS</a>
                </div>--}}
                {{--<div class="col-sm-2">
                    <p style="text-align:right; margin-top: 5px;"><strong>Sale Status</strong></p>
                </div>
                <div style="margin-bottom: 5px;" class="col-sm-2">
                    <div class="btn-group">
                        <button id="status_display_btn" style="min-width: 100px;" type="button" class="btn {{ $sale->status != Utility::SALE_STATUS_CANCELLED ? 'btn-info' : '' }}"><span id="sale_status">{{ Utility::saleStatus()[$sale->status] }} @if($sale->status == Utility::SALE_STATUS_CANCELLED) {{ ($sale->is_cancelled_customer != Utility::SALE_STATUS_CANCELLED_BY_CUST) ? 'by admin' : 'by customer' }} @endif</span></button>
                        @if($sale->status != Utility::SALE_STATUS_CANCELLED)
                            <button id="status_drop_btn" type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul id="change-sale-status" class="dropdown-menu" role="menu">
                                @foreach(Utility::saleStatus() as $value => $sale_status)
                                    <li><a href="{{ route('admin.sales.change_status') }}" data-plugin="ajaxGetRequest" data-type="GET" data-formdata='[{"status":"{{ $value }}","sale":"{{ $sale->id }}"}]' data-conf-message="Do you really want to set the status as cancelled?" data-confdata='{{ Utility::SALE_STATUS_CANCELLED }}'>{{ $sale_status }}</a></li>
                                    <li class="divider"></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>--}} {{--sale status--}}

            <div class="row">
                <div class="col-xs-12">
                    <div class="">
                        <div class="col-xs-10">
                            <address>
                                <strong>Shipping Address:</strong><br>
                                {{ $sale->customer->customer_detail->name }} <br>
                                {!! !empty($sale->address) ? $sale->address['address'] . '<br>' : '' !!}
                                {{ !empty($sale->address) ? $sale->address['place'].', ' : '' }} {{ !empty($sale->address) ? $sale->address['city'].', ' : '' }} {{ !empty($sale->address) ? $sale->address['pincode'].', ' : '' }}<br>
                                {{ !empty($sale->address) ? Utility::district_name($sale->address['district']) .', ' : '' }} {{ !empty($sale->address) ? Utility::state_name($sale->address['state']) .', ' : '' }} India.
                                <br>

                                <strong>
                                    @if($sale->pay_method == Utility::PAYMENT_COD)
                                        <small class="label label-primary">Cash on Delivery</small>
                                    @elseif($sale->pay_method == Utility::PAYMENT_ONLINE)
                                        <small class="label label-primary">Online Payment</small>
                                    @elseif($sale->pay_method == Utility::PAYMENT_OFFLINE)
                                        <small class="label label-primary">Offline Payment</small>
                                    @endif
                                    @if(($sale->pay_method == Utility::PAYMENT_OFFLINE) && (!empty($sale->utr_no)))
                                    <small class="label bg-green">UTR Updated {{ $sale->is_utr_cust ? 'By customer' : 'By Admin'  }}</small>
                                    @endif

                                    @if($sale->delivery_type)
                                    <p><small class="label label-warning">To Pay Shipping</small></p>
                                    @else
                                    <p><small class="label label-warning">Paid Shipping</small></p>
                                    @endif
                                </strong>
                                <br>
                            </address>

                        </div>

                        <div class="col-xs-2 text-center">
                            <address>
                                <br><strong>{!! $sale->is_paid ? '<button style="cursor: not-allowed;" id="pay_status" type="button" class="btn btn-block btn-success btn-lg">Paid</button>' : '<button style="cursor: not-allowed;" id="pay_status" type="button" class="btn btn-block btn-danger btn-lg">Not Paid</button>' !!} </strong>
                                {{--@if($sale->status != Utility::SALE_STATUS_CANCELLED)
                                    <strong id="dv_pay_link" style="line-height: 36px;">
                                        @if($sale->is_paid)
                                            <a href="{{ route('admin.sales.change.payment') }}" id="pay_link" class="text-danger" data-plugin="ajaxGetRequest" data-type="GET" data-formdata='[{"payment":"{{ Utility::SALE_NOTPAID }}","sale":"{{ $sale->id }}"}]' data-conf-message="Do you really want to Cancel the payment?" data-confdata='{{ Utility::SALE_NOTPAID }}'>Cancel Payment</a>
                                        @else
                                            <a href="{{ route('admin.sales.change.payment') }}" id="pay_link" class="text-success" data-plugin="ajaxGetRequest" data-type="GET" data-formdata='[{"payment":"{{ Utility::SALE_PAID }}","sale":"{{ $sale->id }}"}]' data-conf-message="Do you really want to Mark as Paid?" data-confdata='{{ Utility::SALE_PAID }}'>Mark as Paid</a>
                                        @endif
                                    </strong><br>
                                @endif--}}
                            </address>

                        </div>


                    </div>
                </div>
            </div> {{--shipping address details--}}

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Product</th>
                                        <th>HSN Code</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-center">UOM</th>
                                        <th class="text-center">Rate</th>
                                        <th class="text-center">SGST</th>
                                        <th class="text-center">CGST</th>
                                        <th class="text-center">Total</th>
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
                                            <td class="col-md-1 text-center"><i class="fa fa-inr normal"></i>{{ $sale_details->price }}</td>
                                            <td class="col-md-1 text-center">{{ $sale_details->product->sgst() }}%</td>
                                            <td class="col-md-1 text-center">{{ $sale_details->product->cgst() }}%</td>
                                            <td class="col-md-1 text-center"><i class="fa fa-inr normal"></i>{{ $sale_details->price * $sale_details->quantity }}</td>
                                        </tr>
                                        <?php $sNo++; ?>
                                    @endforeach

                                    <tr>
                                        <td colspan="5">   </td>
                                        <td>   </td>
                                        <td colspan="2" class="text-right">
                                            <p>
                                                <strong>Subtotal: </strong>
                                            </p>
                                            <p>
                                                <strong>Shipping Charge : </strong>
                                            </p>
                                        </td>
                                        <td class="text-center">
                                            <p>
                                                <strong><i class="fa fa-inr"></i>{{ $sale->sub_total }}</strong>
                                            </p>
                                            <p>
                                                <strong>{!! $sale->delivery_charge==0 ? '-' : '<i class="fa fa-inr"></i>'.$sale->delivery_charge !!}</strong>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">   </td>
                                        <td>   </td>
                                        <td class="text-right"><h4><strong>Total: </strong></h4></td>
                                        <td class="text-center text-primary"><h4><strong><i class="fa fa-inr"></i>{{ $sale->sub_total + $sale->delivery_charge }}</strong></h4></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{--sales details--}}

        </div>
    </div>
    <!-- /.row -->
</section>
<!-- /.content -->
@stop
@push('post_body')
{{--@include('admin.pages.sales.courier-modal')
@include('admin.pages.sales.sentsms-modal',['sale'=>$sale])--}}
@endpush

@push('page_scripts')
    <script>
        $(document).ready(function(){
            $('#change-sale-status').on('ajax-get-request.success', function (e,data) {
                $('#sale_status').text(data.status + ' ' + data.cancelor );
                if(data.is_paid == 1) {
                    $('#pay_status').text('Paid').addClass('btn-success').removeClass('btn-danger');
                }
                else {
                    $('#pay_status').text('Not Paid').addClass('btn-danger').removeClass('btn-success');
                }
                if(data.status_id == '{{ Utility::SALE_STATUS_CANCELLED }}') {
                    $('#status_drop_btn').remove();
                    $('#change-sale-status').remove();
                    $('#status_display_btn').removeClass('btn-info');
                }
            });

            $('#dv_pay_link').on('ajax-get-request.success', function (e,data) {
                location.reload();
            });

            var pageModal = $('#dvAdd-courier-details');
            pageModal.on('af.success','#add-courier-details-form',function(e,data) {
                $('#tbl_courier_name').text(data.sale.courier_name);
                $('#tbl_courier_web').text(data.sale.courier_website);
                $('#tbl_courier_code').text(data.sale.courier_track);
                $('#tbl_delivery').text(data.sale.delivery);
                $('#courier-add-buton').text('Edit Details');
                pageModal.modal('hide');
            });

            pageModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#add-courier-details-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        courier : {
                            required : true
                        },
                        courier_website : {
                            required : true
                        },
                        courier_track : {
                            required : true
                        }
                    },
                    messages: {
                        courier: "Enter Courier Name",
                        courier_website: "Enter Website",
                        courier_track: "Enter Track Number"
                    }
                });


            });

            var smsModal = $('#dvAdd-sms-details');
            smsModal.on('af.success','#sent-sms-details-form',function(e,data) {
                $('#sms_content').val(data.sale.sms_content);
                smsModal.modal('hide');
            });

            smsModal.on('inside_modal.validation',function(e,data){
                var $el = $(data).clone();
                var formid = '#'+$el.find('#sent-sms-details-form').attr('id');

                var $validator = $(formid).validate({
                    rules : {
                        sms_content : {
                            required : true
                        }
                    },
                    messages: {
                        sms_content: "Enter SMS Content"
                    }
                });


            });

            $('#utr_no_submit').click(function(e) {
                e.preventDefault();
                var utr_no = $('#utr_no').val();

                    var sale_id = '{{ $sale->id }}';
                    var formdata = {utr_no: utr_no, sale_id: sale_id};
                    $.ajax({
                        type: 'POST',
                        data: formdata,
                        url: "{{ route('admin.sales.utr.update') }}",
                        success: function (data) {
                            if (data.success) {
                                location.reload();

                                toastr.success(data.success, null, {
                                    containerId:"toast-topFullWidth",
                                    positionClass:"toast-top-full-width",
                                    showMethod:"slideDown",
                                    closeButton: true
                                });
                            }
                        }
                    });
                /*}*/

            });


        });
    </script>
@endpush
@push('page_styles')
<style>
    .select2-container {
        z-index: 999999;
    }
</style>
@endpush
