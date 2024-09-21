@extends('layouts.app')
@section('title','My Profile')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">My Profile</h3>
                    </div>

                    <div class="col-sm-12">
                        <div class="col-lg-9 col-md-9 profile" style="margin-left: 20px;">
                            <div class="row">
                                <div id="dvData" >
                                    <div class="clearfix">
                                        <div id="update_success" class="alert alert-success" role="alert" style="display: none;">
                                            Data has been updated successfully!
                                        </div>
                                        <div style="position:relative">
                                            <a style="position:absolute; top: 0; right: 60px; font-size: 20px;" id="edit_profile" href="#"><i class="fa fa-pencil"></i></a>
                                            <h4 id="name_data">{{ $customer->customer_detail->name }}</h4>
                                            <h5><strong>{{ $customer->phone }} <span id="phone_alt_data">{{ !empty($customer->customer_detail->address) && !empty($customer->customer_detail->address['phone_alt']) ? ', '.$customer->customer_detail->address['phone_alt'] : '' }}</span></strong></h5>
                                            <strong> <span id="email_data">{{ $customer->email }}</span></strong>
                                        </div>
                                    </div>
                                    @if(!empty($customer->customer_detail->address))
                                        <p id="address_data">
                                            {{ $customer->customer_detail->address['address'] }} <br>
                                            {{ $customer->customer_detail->address['place'] }} <br>
                                            PIN : {{ $customer->customer_detail->address['pincode'] }}, {{ $customer->customer_detail->address['city'] }} <br>
                                            {{ Utility::district_name($customer->customer_detail->address['district']) }} District, {{ Utility::state_name($customer->customer_detail->address['state']) }}
                                        </p>
                                    @else
                                        <p id="address_data"></p>
                                    @endif
                                    <p>GSTIN : {{ isset($customer->customer_detail->gstin) && !empty($customer->customer_detail->gstin) ? $customer->customer_detail->gstin : '' }}</p>
                                </div>
                                <div id="dvForm" class="col-lg-8" style="display: none;">
                                    <div class="">
                                        <form class="" id="profile_form" action="{{ route('customer.profile.update') }}" method="POST" role="form" data-plugin="ajaxForm">
                                            {{ csrf_field() }}
                                            <div class="clearfix">
                                                <div class="form-group">
                                                    <label class="info-title">Name *</label>
                                                    <input id="name" type="text" class="form-control unicase-form-control text-input"  name="name" value="{{ $customer->customer_detail->name }}" >
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title">Email </label>
                                                    <div class="field-widget">
                                                        <input  class="form-control unicase-form-control text-input" type="text" name="email" id="email" value="{{ $customer->email }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title">Alternate Mobile </label>
                                                    <div class="field-widget">
                                                        <input  class="form-control unicase-form-control text-input" type="text" name="phone_alt" id="phone_alt" value="{{ !empty($customer->customer_detail->address) && !empty($customer->customer_detail->address['phone_alt']) ? $customer->customer_detail->address['phone_alt'] : '' }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="info-title">Full address *</label>
                                                    <textarea name="address" class="not-click form-control unicase-form-control text-input" id="address"  style="height: 70px;">{{ isset($customer->customer_detail->address) && !empty($customer->customer_detail->address) ? $customer->customer_detail->address['address'] : '' }}</textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label class="info-title">Place *</label>
                                                        <input type="text" name="place" class="not-click form-control unicase-form-control text-input" id="place" value="{{ isset($customer->customer_detail->address) && !empty($customer->customer_detail->address) ? $customer->customer_detail->address['place'] : '' }}">
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label class="info-title">Pin *</label>
                                                        <input type="text" name="pincode" class="not-click form-control unicase-form-control text-input" id="pincode" value="{{ isset($customer->customer_detail->address) && !empty($customer->customer_detail->address) ? $customer->customer_detail->address['pincode'] : '' }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label class="info-title">Nearest City </label>
                                                        <input type="text" name="city" class="not-click form-control unicase-form-control text-input" id="place" value="{{ isset($customer->customer_detail->address) && !empty($customer->customer_detail->address) ? $customer->customer_detail->address['city'] : '' }}">
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label class="info-title">GSTIN No</label>
                                                        <input type="text" name="gstin" class="not-click form-control unicase-form-control text-input" id="gstin" value="{{ isset($customer->customer_detail->gstin) && !empty($customer->customer_detail->gstin) ? $customer->customer_detail->gstin : '' }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6">
                                                        <label class="info-title">State *</label>
                                                        <select name="state" id="state" onChange="getdistrict(this.value);" class="form-control unicase-form-control text-input">
                                                            <option value="">Select State</option>
                                                            @foreach($states as $state)
                                                                @if(isset($customer->customer_detail->address) && !empty($customer->customer_detail->address))
                                                                    <option value="{{ $state->id }}" {{ $state->id==$customer->customer_detail->address['state'] ? 'selected':'' }}>{{ $state->name }}</option>
                                                                @else
                                                                    <option value="{{ $state->id }}" {{ $state->id==Utility::STATE_ID_KERALA ? 'selected':'' }}>{{ $state->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label class="info-title">District *</label>
                                                        <select name="district" id="district-list" class="form-control unicase-form-control text-input">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <button id="cancel_profile" name="cancel_profile" type="button" style="background-color: #353535; border: 2px solid #353535; cursor: pointer;" class="btn-upper btn btn-primary checkout-page-button">Cancel</button>

                                                        <button type="submit" class="btn-upper btn btn-primary checkout-page-button">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
<script>
    $(document).ready(function() {
        $("#delete_account").click(function(e){
            e.preventDefault();

            var customer_id = "{{ Auth::guard('customer')->user()->id }}";
            var formdata = {customer_id: customer_id};
            var url = "{{ route('delete.account.act') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: formdata,
                success: function (data) {
                    /*console.log(data);*/
                    window.location.replace(data);
                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });

        });

        $('#edit_profile').click(function(e) {
            e.preventDefault();
            $('#dvData').hide();
            $('#dvForm').show();
        });

        $('#cancel_profile').click(function(e) {
            e.preventDefault();
            $('#dvData').show();
            $('#dvForm').hide();
            $('#page_head').focus();
            $("html, body").animate({ scrollTop: 0 }, "slow");
        });

        @if(isset($customer->customer_detail->address) && !empty($customer->customer_detail->address))
            getdistrict('{{ $customer->customer_detail->address['state'] }}');
        @else
            getdistrict('{{ Utility::STATE_ID_KERALA }}');
        @endif

        $('#dvForm').on('af.success','#profile_form',function(e,data) {
            $('#jq-loader').hide();
            $('#dvForm').hide();
            $('#dvData').show();
            $('#name_data').text(data.data.customer_details.name);
            $('#email_data').text(data.data.customer.email);
            $('#phone_alt_data').text(', ' + data.data.customer_details.address['phone_alt']);
            var address_data = data.data.customer_details.address['address'] + '<br>' + data.data.customer_details.address['place'] + '<br> PIN : ' + data.data.customer_details.address['pincode'] + ', ' + data.data.customer_details.address['city'] + '<br>' + data.data.customer_details.address['district'] + ' District, ' + data.data.customer_details.address['state'];
            $('#address_data').html(address_data);
            $('#update_success').show();
            $("html, body").animate({ scrollTop: 0 }, "slow");
        });

        jQuery.validator.addMethod("phoneno", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
        }, "Enter valid phone number");
        var $validator = $('#profile_form').validate({
            rules: {
                name : {
                    required : true
                },
                email : {
                    email : true
                },
                phone_alt : {
                    phoneno : true
                },
                address: {
                    required: true
                },
                pincode: {
                    required: true,
                    digits: true,
                    minlength : 6
                },
                place: {
                    required: true
                },
                state: {
                    required: true
                },
                district: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Name is required"
                },
                email: {
                    email: "Invalid Email"
                },
                address: {
                    required: "Address is required"
                },
                pincode: {
                    required: "PIN Code is required",
                    minlength: "Invalid PIN code",
                    digits: "Invalid PIN code"
                },
                place: {
                    required: "Place is required"
                },
                state: {
                    required: "State is required"
                },
                district: {
                    required: "District is required"
                }
            }

        });
});

    function getdistrict(val) {
        $.ajax({
            type: "POST",
            url: "{{ route('list.districts') }}",
            data:'state_id='+val,
            success: function(data){
                $("#district-list").html(data);
                $("#district-list_alt").html(data);
            }
        });
    }
</script>
@endpush
