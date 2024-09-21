<div class="modal fade" id="mySignupModal">
    <div class="modal-dialog ">
        {{--<form class="login modal-content" method="post" action='' name="login_form">--}}
        <form class="login modal-content" id="signup_form" action="{{ route('customer.register') }}" method="POST" role="form" data-plugin="ajaxForm">
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Login to MyWebsite.com</h3>
            </div>-->
            <div class="modal-body row" >
                <div class="close_btn">
                    <button type="button" class="close" data-dismiss="modal">✕</button>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12" style="padding: 12px 6%;">
                    <h2>Sign up</h2>
                    <p>Please enter your details to continue. We respect and keep all of your personal details confidential.</p>
                </div>
                <div class="right-modal-content col-md-6 col-sm-12 col-xs-12" style="padding: 30px 2%;">
                    {{--<div class="form-group @if ($errors->has('name')) has-error @endif">
                        <input type="text" name="name" id="name" placeholder="Name" />
                        @if ($errors->has('name'))
                            <span class="help-block">
                          {{ $errors->first('name') }}
                      </span>
                        @endif
                    </div>--}}
                    <div class="form-group @if ($errors->has('phone')) has-error @endif">
                        <input type="text" name="phone" id="phone_reg" placeholder="Mobile Number" />
                        @if ($errors->has('phone'))
                            <span class="help-block">
                          {{ $errors->first('phone') }}
                      </span>
                        @endif
                    </div>
                    {{--<div class="form-group @if ($errors->has('email')) has-error @endif">
                        <input type="text" name="email" id="email_su" placeholder="Email" />
                        @if ($errors->has('email'))
                            <span class="help-block">
                          {{ $errors->first('email') }}
                      </span>
                        @endif
                    </div>--}}
                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                        <input type="password" class="" id="password_su" name="password" placeholder="Set Your Password" />
                    </div>
                    <div class="form-group @if ($errors->has('password')) has-error @endif">
                        <input type="password" class="" id="password_confirmation" name="password_confirmation" placeholder="Re-enter Password" >
                        @if ($errors->has('password'))
                            <span class="help-block">
                          {{ $errors->first('password') }}
                      </span>
                        @endif
                    </div>
                    <input type="submit" value="Continue" class="btn btn-lg" />
                    {{--<a data-toggle="modal" href="#myLoginModal" class="btn btn-lg signup_button" >Existing User? Login</a>--}}
                    <div class="remember-forgot">
                        <div class="row">
                            {{--<div class="col-md-12">
                                        <small style="color:#f00">*Email/Mobile Number is Mandatory</small>
                            </div>--}}
                            {{--<div class="col-md-12 forgot-pass-content">
                                <a href="javascript:void(0)" class="forgot-pass pull-right">Forgot Password</a>
                            </div>--}}
                        </div>
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
        $('#mySignupModal').on('af.success','#signup_form',function(e,data) {
            $('#jq-loader').hide();
            $('#mySignupModal').modal('hide');
            $('#myOtpModal').modal('show');
        });

        jQuery.validator.addMethod("phoneno", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
        }, "Enter valid phone number");
        var $validator = $('#signup_form').validate({
            rules: {
                /*email: {
                    required: true,
                    email: true
                },*/
                phone : {
                    required : true,
                    phoneno : true
                }
            },
            messages: {
                /*email: {
                    required: "Email is required"
                },*/
                phone: {
                    required: "Phone is required"
                }
            }
        });


    });
</script>
@endpush