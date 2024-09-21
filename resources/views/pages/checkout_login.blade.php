@extends('layouts.app')
@section('title','LOGIN OR SIGNUP')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">
        <div class="container">
            <div class="row">
                <div class="col-md-8 khm-cart khm-checkout">
                    <div class="panel panel-info" style="margin-top: 10px;">
                        <div class="panel-heading" style="background: #2874f0;">
                            <div class="panel-title">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h5 class="my_checkout"><small class="checkout_no">1</small> LOGIN OR SIGNUP</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="dvEmptycontainer01">
                            <div class="panel-body">
                                {{-- <form class="login" action="{{ route('customer.login') }}" method="post" id="login_form_chkout" role="form" data-laddabutton="#login-submit" data-plugin="ajaxForm">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <div class="form-group @if ($errors->has('phone')) has-error @endif">
                                                <div style="position: relative;">
                                                    <input type="text" name="phone" id="phone_chkout" placeholder="Enter Mobile Number" autocomplete="off" />
                                                    <a href="javascript:void(0)" id="change-phone2" class="forgot-pass login-absolute" style="display:none;">Change?</a>
                                                    @if ($errors->has('phone'))
                                                        <span class="help-block">
                                                          {{ $errors->first('phone') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div id="dv_check_user2" class="form-group">
                                                <input id="check_user2" type="button" value="Continue" class="btn btn-lg" />
                                            </div>

                                            <div id="dvforget-pwd2" class="form-group @if ($errors->has('password')) has-error @endif" style="display: none;">
                                                <div style="position: relative;">
                                                    <input type="password" name="password" id="password_chkout" placeholder="Enter Password" />
                                                    <a href="javascript:void(0)" id="forget-pwd2" class="forgot-pass login-absolute forget-pwd2">Forgot?</a>
                                                </div>
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                      {{ $errors->first('password') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div id="dvotp-pwd-detail2" class="form-group" style="display: none;">
                                                <div style="position: relative;">
                                                    <label>OTP sent to Mobile</label>
                                                    <a href="javascript:void(0)" class="forgot-pass login-absolute forget-pwd2">Resend?</a>
                                                </div>
                                            </div>
                                            <div id="dvotp-pwd2" class="form-group @if ($errors->has('nw_otp')) has-error @endif" style="display: none;">
                                                <div style="position: relative;">
                                                    <input type="text" id="nw_otp2" name="nw_otp" placeholder="Enter OTP" />
                                                </div>
                                                @if ($errors->has('nw_otp'))
                                                    <span class="help-block">
                                                        {{ $errors->first('nw_otp') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <div id="dvset-pwd2" class="form-group" style="display: none;">
                                                <div style="position: relative;">
                                                    <input type="password" id="set_password2" name="set_password" placeholder="Set Password" />
                                                </div>

                                            </div>

                                            <div id="dv_login_btn2" class="col-xs-12 col-md-12" style="display: none;">
                                                <button type="submit" id="login-submit" class="btn btn-lg ladda-button" data-style="zoom-out" style="color: #fff; font-weight: bold" ><span class="ladda-label">CONTINUE</span></button>
                                            </div>

                                        </div>

                                    </div>
                                </form> --}}
                                <div id="dv_check_user2" class="form-group">
                                    <a href="{{ route('customer.redirect.gmail') }}" class="btn btn-lg" >Login with your Gmail</a>
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
                    @include('partial.price-detail-sidebar')
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

        var dvEmptycontainer01 = $('#dvEmptycontainer01');
        dvEmptycontainer01.on('click','#change-phone2',function(e) {
            e.preventDefault();
            $('#phone_chkout').val('').focus().removeAttr('readonly');
            $('#password_chkout').val('');
            $('#nw_otp2').val('');
            $('#set_password2').val('');
            $('#dvotp-pwd-detail2').hide();
            $('#dvotp-pwd2').hide();
            $('#dvset-pwd2').hide();
            $('#dv_check_user2').show();
            $('#dvforget-pwd2').hide();
            $('#dv_login_btn2').hide();
            $('#change-phone2').hide();
        });

        dvEmptycontainer01.on('click','.forget-pwd2',function(e) {
            e.preventDefault();
            sendOtp2();
        });


        $('#dvEmptycontainer01').on('af.success','#login_form_chkout',function(e,data) {
            window.location.replace("{{ route('checkout.address') }}");
        });

        jQuery.validator.addMethod("validphone", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            isPhone = this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
            return isPhone;
        }, "Please specify a valid Phone");

        var $validator = $('#login_form_chkout').validate({
            rules: {
                phone: {
                    required: true,
                    validphone: true
                },
                password : {
                    required : true,
                    minlength: 6
                },
                nw_otp : {
                    required : true,
                    minlength: 6
                },
                set_password : {
                    required : true,
                    minlength: 6
                }
            },
            messages: {
                phone: {
                    required: "Enter Phone"
                },
                password: {
                    required: "Enter Password",
                    minlength: "Password must have 6 charecters"
                },
                nw_otp: {
                    required: "Enter OTP",
                    minlength: "Invalid OTP"
                },
                set_password: {
                    required: "Enter new password",
                    minlength: "Password must have 6 charecters"
                }
            }
        });

        $(document).on('click','#check_user2',function(e) {
            e.preventDefault();
            var customer_phone = $('#phone_chkout').val();
            var url = "{{ route('customer.check') }}";
            var form_data = {phone: customer_phone};
            $.ajax({
                type: "POST",
                url: url,
                data: form_data,
                success: function (data) {
                    console.log('success');
                    if(data.is_exist==1) {
                        $('#phone_chkout').attr('readonly','readonly');
                        $('#dv_check_user2').hide();
                        $('#dvforget-pwd2').show();
                        $('#dv_login_btn2').show();
                        $('#change-phone2').show();
                        console.log('1');
                    }
                    else {
                        sendOtp2();
                        $('#phone_chkout').attr('readonly','readonly');
                        $('#dv_check_user2').hide();
                        $('#dv_login_btn2').show();
                        $('#change-phone2').show();
                        console.log('2');
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });
        });

    });

    function sendOtp2() {
        console.log('enter otp');
        var ph_valid = $('#phone_chkout').val();
        var isph_valid = ph_valid.length == 10 &&
                ph_valid.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
        if(isph_valid==false) {

        }else {
            $('#dvforget-pwd2').hide();
            $('#nw_otp2').val('');
            $('#set_password2').val('');
            $('#password_chkout').val('');
            $('#dvotp-pwd-detail2').show();
            $('#dvotp-pwd2').show();
            $('#dvset-pwd2').show();

            var input_username = $('#phone_chkout').val();
            var url = "{{ route('customer.future.otp') }}";
            var form_data = {input_username: input_username};
            $.ajax({
                type: "POST",
                url: url,
                data: form_data,
                success: function (data) {

                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });
        }
    }
</script>

@endpush
