@extends('layouts.affiliate')
@section('title','DELIVERY ADDRESS')
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
                    <div class="panel panel-info" style="margin-top: 10px;">
                        <div class="panel-heading" style="background: #2874f0;">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_checkout"><small class="checkout_no">2</small> DELIVERY ADDRESS</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="dvEmptycontainer01">
                            <div class="panel-body">
                                <form class="login" action="{{ route('affiliate.checkout.address.store',$affiliate_slug) }}" method="post" id="address_form_chkout" data-laddabutton="#address-submit" role="form" data-plugin="ajaxForm">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-9">
                                            <div class="form-group col-md-12 @if ($errors->has('name')) has-error @endif">
                                                <input type="text" name="name" id="name_chkout" placeholder="Full Name" value="{{ $customerDetails->name }}" />
                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                                      {{ $errors->first('name') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6 @if ($errors->has('pincode')) has-error @endif">
                                                <input type="text" name="pincode" id="pincode" placeholder="PIN Code" value="{{ isset($customerDetails->address) && !empty($customerDetails->address) ? $customerDetails->address['pincode'] : '' }}" />
                                                @if ($errors->has('pincode'))
                                                    <span class="help-block">
                                                      {{ $errors->first('pincode') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6 @if ($errors->has('place')) has-error @endif">
                                                <input type="text" name="place" id="place" placeholder="Locality" value="{{ isset($customerDetails->address) && !empty($customerDetails->address) ? $customerDetails->address['place'] : '' }}" />
                                                @if ($errors->has('place'))
                                                    <span class="help-block">
                                                      {{ $errors->first('place') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-12 @if ($errors->has('address')) has-error @endif">
                                                <textarea type="password" name="address" id="address_chkout" placeholder="Address (area and street)" style="height: 70px;" >{{ isset($customerDetails->address) && !empty($customerDetails->address) ? $customerDetails->address['address'] : '' }}</textarea>
                                                @if ($errors->has('address'))
                                                    <span class="help-block">
                                                      {{ $errors->first('address') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6 @if ($errors->has('district')) has-error @endif">
                                                <select name="district" id="district-list"  placeholder="District">
                                                    <option value="">Select District</option>
                                                </select>
                                                {{--<input type="text" name="district" id="district" placeholder="District" />--}}
                                                @if ($errors->has('district'))
                                                    <span class="help-block">
                                                      {{ $errors->first('district') }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6 @if ($errors->has('state')) has-error @endif">
                                                <select name="state" id="state" onChange="getdistrict(this.value);"  placeholder="State">
                                                    <option value="">Select State</option>
                                                    @foreach($states as $state)
                                                        @if(isset($customerDetails->address) && !empty($customerDetails->address))
                                                            <option value="{{ $state->id }}" {{ $state->id==$customerDetails->address['state'] ? 'selected':'' }}>{{ $state->name }}</option>
                                                        @else
                                                            <option value="{{ $state->id }}" {{ $state->id==18 ? 'selected':'' }}>{{ $state->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('state'))
                                                    <span class="help-block">
                                                      {{ $errors->first('state') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6 @if ($errors->has('phone')) has-error @endif">
                                                <input type="text" name="phone" id="phone" placeholder="Mobile Number" value="{{ isset($customerDetails->customer->phone) && !empty($customerDetails->customer->phone) ? $customerDetails->customer->phone : '' }}" />
                                                @if ($errors->has('phone'))
                                                    <span class="help-block">
                                                      {{ $errors->first('phone') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6">
                                                <input type="text" name="phone_alt" id="phone_alt" placeholder="Alternative Contact (optional)" value="{{ isset($customerDetails->address) && !empty($customerDetails->address) ? $customerDetails->address['phone_alt'] : '' }}" />
                                            </div>

                                            {{-- <div class="form-group col-md-6">
                                                <input type="text" name="email" id="email" placeholder="Email (optional)" value="{{  Auth::guard('customer')->user()->email }}" />
                                            </div> --}}

                                            <div class="form-group col-md-6 @if ($errors->has('city')) has-error @endif">
                                                <input type="text" name="city" id="city" placeholder="Nearest City (optional)" value="{{ isset($customerDetails->address) && !empty($customerDetails->address) ? $customerDetails->address['city'] : '' }}" />
                                                @if ($errors->has('city'))
                                                    <span class="help-block">
                                                      {{ $errors->first('city') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group col-md-6">
                                                <input type="text" name="gstin" id="gstin" placeholder="GSTIN No (optional)" value="{{ isset($customerDetails->gstin) && !empty($customerDetails->gstin) ? $customerDetails->gstin : '' }}" />
                                            </div>
                                            <div class="form-group col-md-6 pull-right">
                                                {{--<input id="address-submit" type="submit" value="CONTINUE" class="btn btn-lg" style="color: #fff; font-weight: bold" />--}}
                                                <button id="address-submit" type="submit" class="btn btn-lg ladda-button" data-style="zoom-out" style="color: #fff; font-weight: bold" ><span class="ladda-label">CONTINUE</span></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-info inactive" style="margin-top: 10px;">
                        <div class="panel-heading" style="background: #fff;">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_checkout"><small class="checkout_no inactive">3</small> PAYMENT OPTIONS</h5>
                                    </div>
                                </div>
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

        refreshCart();
        $('#dvEmptycontainer01').on('af.success','#address_form_chkout',function(e,data) {
            window.location.replace("{{ route('affiliate.checkout.payment_options',$affiliate_slug) }}");
        });

        var $validator = $('#address_form_chkout').validate({
            rules: {
                name: {
                  required: true
                },
                address: {
                  required: true
                },
                phone: {
                  required: true
                },
                place: {
                  required: true
                },
                pincode: {
                  required: true,
                    digits: true,
                    minlength : 6
                },
                district: {
                    required: true
                },
                state : {
                    required : true
                }
            },
            messages: {
                name: {
                    required: "Name is required"
                },
                address: {
                    required: "Address is required"
                },
                phone: {
                    required: "Phone is required"
                },
                place: {
                    required: "Place is required"
                },
                pincode: {
                    required: "PIN code is required",
                    minlength: "Invalid PIN code",
                    digits: "Invalid PIN code"
                },
                district: {
                    required: "District is required"
                },
                state: {
                    required: "State is required"
                }
            }
        });

        @if(isset($customerDetails->address) && !empty($customerDetails->address))
            getdistrict('{{ $customerDetails->address['state'] }}');
        @else
            getdistrict('{{ Utility::STATE_ID_KERALA }}');
        @endif


    });
</script>

<script>
    function getdistrict(val) {
        $.ajax({
            type: "POST",
            url: "{{ route('list.districts') }}",
            data:'state_id='+val,
            success: function(data){
                $("#district-list").html(data);
            }
        });
    }

</script>
@endpush
