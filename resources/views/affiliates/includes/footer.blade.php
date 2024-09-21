@if(!empty($affiliate->footer_description))

    <div class="wrapper sub_page_pt" style="color: lightblue;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    {!! $affiliate->footer_description !!}
                </div>
            </div>
        </div>
    </div>
@endif
<div class="clearfix"></div>
<!-- Footer Start -->
<footer class="footer-bs">
    <div class="row">
        <div style="padding-right: 0px;" class="col-md-2 col-sm-4 footer-social animated fadeInDown">
            <h4>{{ $affiliate->user->name }}</h4>
            <ul class="list">
                <li><a href="{{ route('affiliate.about',$affiliate_slug) }}">About Us</a></li>
                <li><a href="{{ route('affiliate.contact',$affiliate_slug) }}">Contact Us</a></li>
            </ul>
        </div>
        <div class="col-md-2 col-sm-4 footer-social animated fadeInDown">
            <h4>Explore New</h4>
            <ul>
                <li><a href="{{ route('affiliate.brands',$affiliate_slug) }}">Brands</a></li>
                {{-- <li><a href="{{ route('affiliate.services',$affiliate_slug) }}">Services</a></li> --}}
            </ul>
        </div>
        <div class="col-md-2 col-sm-4 footer-social animated fadeInDown">
            <h4>Help</h4>
            <ul class="list">
                <li><a href="{{ route('affiliate.terms_conditions',$affiliate_slug) }}">Terms & Condition</a></li>
                <li><a href="{{ route('affiliate.shipping',$affiliate_slug) }}">Shipping/Membership Policy</a></li>
                {{--<li><a href="{{ route('affiliate.payments', $affiliate_slug) }}">Payments</a></li>--}}
                <li><a href="{{ route('affiliate.disclaimer', $affiliate_slug) }}">Disclaimer Policy</a></li>
                <li><a href="{{ route('affiliate.privacy_policy',$affiliate_slug) }}">Privacy Policy</a></li>
                <li><a href="{{ route('affiliate.cancellation',$affiliate_slug) }}">Cancellation & Return Policy</a></li>
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

        </div>
        <div class="col-md-3 footer-brand animated fadeInLeft" style="padding-bottom: 0px;text-align: justify;">
            <h2><a href="{{ route('all.slug',$affiliate_slug) }}"><img src="{{ empty($affiliate->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Affiliate::FILE_DIRECTORY .  '/' . $affiliate->image['original']) }}" width="160" alt="{{ $affiliate->user->name }}" style="background-color: #2874f0;"/></a></h2> {{--filter: invert(100%);--}}
            {!! $affiliate->description !!}

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
