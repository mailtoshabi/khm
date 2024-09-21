<div class="modal fade" id="myLoginModal">
    <div class="modal-dialog ">
        <form class="login modal-content" action="{{ route('customer.login') }}" method="post" id="login_form" role="form" data-plugin="ajaxForm" autocomplete="off">
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Login to MyWebsite.com</h3>
            </div>-->
            <div class="modal-body row" >
                <div class="close_btn">
                    <button type="button" class="close" data-dismiss="modal">✕</button>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12" style="padding: 12px 6%;">
                    {{--<h2 style="font-size: 23px">Kerala Health Mart</h2>--}}
                    <h3 style="font-size: 18px;">Login</h3>
                    <p>Please enter your details to continue</p>
                </div>
                <div class="right-modal-content col-md-6 col-sm-12 col-xs-12 signup_modal_pad" >
                    <div class="form-group @if ($errors->has('phone')) has-error @endif" style="margin-bottom: 0px;">
                        <div style="position: relative;">
                            <input type="text" name="phone" id="phone" placeholder="Enter Mobile Number" />
                            <a href="javascript:void(0)" id="change-phone" class="forgot-pass login-absolute" style="display:none;">Change?</a>
                            @if ($errors->has('phone'))
                            <span class="help-block">
                              {{ $errors->first('phone') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div id="dv_check_user" class="form-group" >
                        <input id="check_user" type="button" value="Continue" class="btn btn-lg" />
                    </div>



        {{-- <form class="login modal-content" action="{{ route('customer.redirect.gmail') }}" method="get" >
            <input type="submit" value="Login with Gmail" class="btn btn-lg" />
        </form> --}}

                    <div id="dvforget-pwd" class="form-group @if ($errors->has('password')) has-error @endif " style="display: none;">
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" placeholder="Password" />
                            <a href="javascript:void(0)" id="forget-pwd" class="forgot-pass login-absolute forget-pwd">Forgot?</a>
                        </div>
                        @if ($errors->has('password'))
                        <span class="help-block">
                            {{ $errors->first('password') }}
                        </span>
                        @endif

                    </div>
                    <div id="dvotp-pwd-detail" class="form-group" style="display: none;">
                        <div style="position: relative;">
                            <label>OTP sent to Mobile</label>
                            <a href="javascript:void(0)" class="forgot-pass login-absolute forget-pwd">Resend?</a>
                        </div>
                    </div>
                    <div id="dvotp-pwd" class="form-group @if ($errors->has('nw_otp')) has-error @endif" style="display: none;">
                        <div style="position: relative;">
                            <input type="text" id="nw_otp" name="nw_otp" placeholder="Enter OTP" />
                        </div>
                        @if ($errors->has('nw_otp'))
                            <span class="help-block">
                            {{ $errors->first('nw_otp') }}
                        </span>
                        @endif
                    </div>

                    <div id="dvset-pwd" class="form-group" style="display: none;">
                        <div style="position: relative;">
                            <input type="password" id="set_password" name="set_password" placeholder="Set Password" />
                        </div>

                    </div>
                    <div id="dv_login_btn" class="form-group" style="display: none;">
                        <input type="submit" value="Login" class="btn btn-lg" />
                    </div>
            </div>
            </div>
            <!--<div class="modal-footer">
                New To MyWebsite.com?
                <a href="#" class="btn btn-primary">Register</a>
            </div>-->
        </form>
    </div>
</div>
@push('page_scripts')
<script>
    $(document).ready(function() {
        var myLoginModal = $('#myLoginModal');
        myLoginModal.on('af.success','#login_form',function(e,data) {
            $('#jq-loader').hide();
            $('#myLoginModal').modal('hide');
            location.reload();
        });

        myLoginModal.on('click','#change-phone',function(e) {
            e.preventDefault();
            $('#phone').val('').focus().removeAttr('readonly');
            $('#password').val('');
            $('#nw_otp').val('');
            $('#set_password').val('');
            $('#dvotp-pwd-detail').hide();
            $('#dvotp-pwd').hide();
            $('#dvset-pwd').hide();
            $('#dv_check_user').show();
            $('#dvforget-pwd').hide();
            $('#dv_login_btn').hide();
            $('#change-phone').hide();
        });

        myLoginModal.on('click','.forget-pwd',function(e) {
            e.preventDefault();
            sendOtp();
        });

        $(document).on('click','#check_user',function(e) {
            e.preventDefault();
            var customer_phone = $('#phone').val();
            var url = "{{ route('customer.check') }}";
            var form_data = {phone: customer_phone};
            $.ajax({
                type: "POST",
                url: url,
                data: form_data,
                success: function (data) {
                    if(data.is_exist==1) {
                        $('#phone').attr('readonly','readonly');
                        $('#dv_check_user').hide();
                        $('#dvforget-pwd').show();
                        $('#dv_login_btn').show();
                        $('#change-phone').show();
                    }
                    else {
                        sendOtp();
                        $('#phone').attr('readonly','readonly');
                        $('#dv_check_user').hide();
                        /*$('#password').val('');*/
                        /*$('#nw_otp').val('');*/
                        /*$('#set_password').val('');*/
                        /*$('#dvforget-pwd').hide();*/
                        $('#dv_login_btn').show();
                        $('#change-phone').show();
                        /*$('#dvotp-pwd-detail').show();
                        $('#dvotp-pwd').show();
                        $('#dvset-pwd').show();*/
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });
        });
        $(document).on('click','#send_otp',function(e) {
            e.preventDefault();
            $('#myLoginModal').modal('hide');
            $('#myOtpModal').modal('show');
            var input_username = $('#phone').val();
            var url = "{{ route('customer.future.otp') }}";
            var form_data = {input_username: input_username};
            $.ajax({
                type: "POST",
                url: url,
                data: form_data,
                success: function (data) {
                    /*$('#mycart-total-quantity').text(data.cart_total);*/
                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });

        });

        jQuery.validator.addMethod("validphone", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            isPhone = this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
            return isPhone;
        }, "Please specify a valid Phone");

        var $validator = $('#login_form').validate({
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


    });

    function sendOtp() {
        var ph_valid = $('#phone').val();
        var isph_valid = ph_valid.length == 10 &&
                ph_valid.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
        if(isph_valid==false) {

        }else {
            $('#dvforget-pwd').hide();
            $('#nw_otp').val('');
            $('#set_password').val('');
            $('#password').val('');
            $('#dvotp-pwd-detail').show();
            $('#dvotp-pwd').show();
            $('#dvset-pwd').show();

            var input_username = $('#phone').val();
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
