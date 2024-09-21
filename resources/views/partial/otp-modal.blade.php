<div class="modal fade" id="myOtpModal">
    <div class="modal-dialog ">
        {{--<form class="login modal-content" method="post" action='' name="login_form">--}}
        <form class="login modal-content" id="otp_verify_form" action="{{ route('customer.validate.otp') }}" method="POST" role="form" data-plugin="ajaxForm">
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Login to MyWebsite.com</h3>
            </div>-->
            <div class="modal-body row" >
                <div class="close_btn">
                    <button type="button" class="close" data-dismiss="modal">✕</button>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12" style="padding: 12px 6%;">
                    <h2>Enter OTP</h2>
                    <p>Check your Mobile for OTP</p>
                </div>
                <div class="right-modal-content col-md-6 col-sm-12 col-xs-12" style="padding: 30px 2%;">
                    <div class="form-group @if ($errors->has('invalid_otp')) has-error @endif">
                        <input type="text" name="khm_otp" id="khm_otp" placeholder="Enter 6 digit OTP" />
                        @if ($errors->has('invalid_otp'))
                            <span class="help-block">
                              {{ $errors->first('invalid_otp') }}
                          </span>
                        @endif
                    </div>
                    <input type="submit" value="Submit" class="btn btn-lg" />
                    <div class="remember-forgot">
                        <div class="row">
                            <div class="col-md-12">
                                <small style="color:#f00"><a href="#">Resend OTP</a></small>
                            </div>
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
        $('#myOtpModal').on('af.success','#otp_verify_form',function(e,data) {
            $('#jq-loader').hide();
            $('#myOtpModal').modal('hide');
           location.reload();
        });

        /*   .on('af.error', function (e, responseText, textStatus, jqXHR, errorThrown) {
            if (jqXHR.status == 422) {
                responseText = $.parseJSON(responseText);
                var error = {};
                $.each(responseText, function (k, v) {
                    error[k] = v[0];
                });

                $validator.showErrors(error);
            } else {
                window.Site.toast.error("Some error occurred, Please try again");
            }

            //some error occured.
        });*/

        var $validator = $('#otp_verify_form').validate({
            rules: {
                'khm_otp': {
                    required: true
                }
            },
            messages: {
                'khm_otp': {
                    required: "Enter OTP"
                }
            }
        });
    });
</script>
@endpush