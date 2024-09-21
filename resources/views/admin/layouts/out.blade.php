@include('admin.includes.head')

<link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/iCheck/square/blue.css') }}">
</head>
<body class="hold-transition login-page">
<div class="wrapper">
            @yield('content')

            {{--<footer class="main-footer" style="margin-left: 0px;">
                @include('admin.includes.footer')
            </footer>--}}
                </div>

</body>
<!-- jQuery 2.2.3 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'bootstrap/js/bootstrap.min.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/iCheck/icheck.min.js') }}"></script>
@stack('page_scripts')
<script>
    /*$(document).ready(function(){
        resizeDiv();
    });

    window.onresize = function(event) {
        resizeDiv();
    }

    function resizeDiv() {
        vpw = $(window).width();
        vph = $(window).height();
        $('.login-box').css({'height': vph + 'px'});
    }*/

    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</html>
