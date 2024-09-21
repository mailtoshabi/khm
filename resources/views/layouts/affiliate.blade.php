<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="index, follow"/>
    <meta name="distribution" content="Global"/>
    <meta name="language" content="en-US"/>
    <meta name="doc-type" content="Public"/>
    <meta name="rating" content="General"/>
    <meta name="audience" content="all"/>
    <meta name="resource-type" content="document"/>
    @hasSection('description')
    <meta name="description" content="@yield('description')">
    @else
    <meta name="description" content="{{ Utility::settings('site_description') }}">
    @endif
    @hasSection('keywords')
    <meta name="keywords" content="@yield('keywords')">
    @else
    <meta name="keywords" content="{{ Utility::settings('site_keywords') }}">
    @endif
    <meta name="author" content="Web Mahal Web Services">
    <meta name="copyright" content="Kerala Healthmart">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @hasSection('og_content')
    @yield('og_content')
    @else
        <meta property="og:title" content="{{ $affiliate->user->name }}"/>
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="{{ config('app.website_url') }}/{{ $affiliate_slug }}"/>
        <meta property="og:site_name" content="{{ config('app.website_url') }}/{{ $affiliate_slug }}"/>
        <meta property="og:description" content="{{ strip_tags(Utility::settings('site_description')) }}"/>
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @hasSection('title')
    <title>{{ $affiliate->user->name }} | @yield('title')</title>
    @else
    <title>{{ Utility::settings('site_title') }} | {{ $affiliate->user->name }}</title>
    @endif
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">

    <link href="{{ asset('dist/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('dist/css/global.css') }}" rel="stylesheet" type="text/css">
    <!--Banner-->
    <link href="{{ asset('dist/css/jquerysctipttop.css') }}" rel="stylesheet" type="text/css">

    <!--Fonts-->
    <link href="{{ asset('fonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('fonts/glyphicons/glyphicons.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap-select.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/ladda-bootstrap/ladda.min.css?v2.2.0') }}">
    @stack('page_style')
    <script>
        (function(i,s,o,g,r,a,m){
            i['GoogleAnalyticsObject']=r;
            i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},
                    i[r].l=1*new Date();
            a=s.createElement(o),m=s.getElementsByTagName(o)[0];
            a.async=1;
            a.src=g;
            m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-138655621-1', 'auto');
        ga('send', 'pageview');
    </script>
    {{--<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>--}}
    {{--<script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-2286206576585313",
            enable_page_level_ads: true
        });
    </script>--}}
</head>
<body>
<div id="jq-loader">
    <img src="{{ asset('images/jq-loader/jq-loader.gif') }}" alt="">
</div>

<div id="msg-bg"></div>

@include('affiliates.includes.headers')
<!-- main start -->
<div class="main">
    @include('affiliates.includes.nav')
    @yield('content')
    @include('partial.login-modal')
    @include('partial.signup-modal')
    @include('partial.otp-modal')
    {{-- @include('affiliates.includes.upload-prescrip-modal') --}}
    @include('affiliates.includes.transfer-payment-modal')
    @stack('page_html')
</div>
<!-- main end -->
@include('affiliates.includes.footer')
<script src="{{ asset('dist/js/jquery.min.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('dist/js/global.js') }}"></script>
<script src="{{ asset('dist/js/bootstrap-select.js') }}"></script>
<script src="{{ asset('vendor/ladda-bootstrap/spin.min.js') }}"></script>
<script src="{{ asset('vendor/ladda-bootstrap/ladda.min.js') }}"></script>
<!-- VALIDATOR -->
<script src="{{ asset('vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-validation/additional-methods.min.js') }}"></script>
<!--// VALIDATOR -->
@stack('page_scripts')
<script>

    $(document).ready(function() {
        $(document).on('click', '.clickSignupModal', function () {
            $('#myLoginModal').modal('hide');
        });
    });

    function refreshCart() {
        $.ajax({
            type: "GET",
            url: "{{ route('product.cart.refresh') }}",
            success: function(data){
                $('#total_quantity_cart').text(data.total_quantity);
                $('#total_quantity_my_cart').text(data.total_quantity);
                $('#total_quantity').text(data.total_quantity);
                $('#total_amount').text(parseFloat(data.total_amount).toFixed(2));
                $('#amount_payable').text(parseFloat(data.amount_payable).toFixed(2));
                if(data.delivery['cost']==0) {
                    $('#delivery_charge').html(data.delivery['display']);
                }else {
                    var inr ='<i class="fa fa-inr"></i>';
                    $('#delivery_charge').html(inr + parseFloat(data.delivery['cost']).toFixed(2));
                }


                /*console.log(data.delivery);*/
                if(data.delivery['cost'] ==0) {
                    $('#delivery_note').hide();
                }else {
                    $('#delivery_note').show();
                }

                if(data.ship_option ==0) {
                    $('#dvPackaging').show();
                    $('#ship_note').hide();
                }else {
                    $('#dvPackaging').hide();
                    $('#ship_note').show();
                }

            }
        });
    }

    function refreshPage() {
        location.reload();
    }
    function goBack() {
        window.history.back();
    }

</script>

<!--Start of Tawk.to Script-->
{{--<script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement
        ("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5a9fa2d6d7591465c708559b/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
</script>--}}
<!--End of Tawk.to Script-->
</body>
</html>
