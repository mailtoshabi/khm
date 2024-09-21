<div class="clearfix"></div>
<!-- Footer Start -->
<footer class="footer-bs">
    <div class="row">

        <div style="padding-right: 0px;" class="col-md-2 col-sm-4 footer-social animated fadeInDown">
            <h4>Kerala Health Mart</h4>
            <ul class="list">
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
                <li><a href="{{ route('affiliate') }}">Go Online Now</a></li>
            </ul>
        </div>
        <div class="col-md-2 col-sm-4 footer-social animated fadeInDown">
            <h4>Explore New</h4>
            <ul>
                <li><a href="{{ route('brands') }}">Brands</a></li>
                {{-- <li><a href="{{ route('services') }}">Services</a></li> --}}
                <li><a href="{{ route('affiliates') }}">Stores</a></li>
            </ul>
        </div>
        <div class="col-md-2 col-sm-4 footer-social animated fadeInDown">
            <h4>Help</h4>
            <ul class="list">
                <li><a href="{{ route('terms_conditions') }}">Terms & Condition</a></li>
                <li><a href="{{ route('shipping') }}">Shipping/Membership Policy</a></li>
                {{--<li><a href="{{ route('payments') }}">Payments</a></li>--}}
                <li><a href="{{ route('disclaimer') }}">Disclaimer Policy</a></li>
                <li><a href="{{ route('privacy_policy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('cancellation') }}">Cancellation & Return Policy</a></li>
            </ul>
        </div>
        <div id="dvnewsletter" class="col-md-3 footer-ns animated fadeInRight">
            <h4>Newsletter</h4>
            <p>Keep updated with our latest offers</p>
            <p id="msg" style="display:none"></p>
            <p>
            <form method="POST" name="form-subscribe" id="form-subscribe" action="{{ route('subscribe') }}" data-plugin="ajaxForm">
                {!! csrf_field() !!}
                <div class="input-group">
                    <input type="text" class="form-control" name="phone" id="phone_subscribe" placeholder="Type your Mobile number here">
                        <span class="input-group-btn">
                          <button class="btn btn-default" type="submit" name="submit-subscribe" id="submit-subscribe"><span class="fa fa-send-o"></span></button>
                        </span>
                </div><!-- /input-group -->
            </form>
            </p>
            <p style="margin-top: 15px; font-size: 15px;"><a href="https://play.google.com/store/apps/details?id=com.keralahealthmart.app" target="_blank">Download Mobile App here</a></p>

            <div class="footer-social" style="padding-top: 10px; text-align: justify; padding-left: 0;">
                <span href="#">Stay Connected :</span>
                <a href="https://www.facebook.com/Keralahealthmart/" target="_blank"><i id="social-fb" class="fa fa-facebook-square fa-3x social"></i></a>
                <a href="https://twitter.com/Keralahealthmar" target="_blank"><i id="social-tw" class="fa fa-twitter-square fa-3x social"></i></a>
                <a href="https://www.instagram.com/keralahealthmart" target="_blank"><i id="social-ig" class="fa fa-instagram fa-3x social"></i></a>
                <a href="https://plus.google.com/112721337652678357963" target="_blank"><i id="social-gp" class="fa fa-google-plus-square fa-3x social"></i></a>
                <a href="https://www.youtube.com/channel/UCI_eNSQr6fB69BXF9IJBXhQ" target="_blank"><i id="social-em" class="fa fa-youtube-square fa-3x social"></i></a>
            </div>



        </div>
        <div class="col-md-3 footer-brand animated fadeInLeft" style="padding-bottom: 0px;text-align: justify;">
            <h2><a href="{{ route('index') }}"><img src="{{ asset('images/logo_footer.png') }}" width="160" alt="kerala health mart" style=""/></a></h2> {{--filter: invert(100%);--}}
            <p><strong>Kerala Health Mart</strong> specializes in marketing and distribution of branded medical products. Our mission is to offer a robust technology-powered platform to enable a seamless flow of healthcare products and services in Kerala. At Kerala health mart, we are ready to expand our horizons.<a href="{{ route('about') }}">+More</a></p>
        </div>
    </div>
    <hr style="margin: 0px 0 15px; border-top: 2px solid #eee;">

            <p class="col-md-12" style="padding-left: 0;">© {{ Carbon\Carbon::now()->format('Y') }} {{ config('app.name') }}<sup>TM</sup>, All rights reserved. <span>Proudly Powered By <a href="https://webmahal.com" target="_blank">WEB MAHAL</a></span> </p>

    </div>
</footer>
<!-- Footer End -->
@push('page_scripts')
<script>
    $(document).ready(function() {
        jQuery.validator.addMethod("phoneno", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
        }, "Enter valid phone number");
        var $validator = $('#form-subscribe').validate({
            rules: {
                phone: {
                    required: true,
                    phoneno: true
                }
            },
            messages: {
                phone: {
                    required: "Phone is required"
                }
            }
        });

        $('#dvnewsletter').on('af.success','#form-subscribe',function(e,data) {
            $('#jq-loader').hide();
            if(data.subscribe ==1) {
                $('#msg').css("color","#00a65a").show().text('You have been successfully subscribed to our news letters.');
                $('#phone_subscribe').val('');
            }else {
                $('#msg').css("color","red").show().text('You have already been subscribed to our news letters.');
            }
        });
    });
</script>

@endpush
