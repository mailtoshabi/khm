<!-- jQuery 2.2.3 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
@yield('p_scripts')
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/toastr/toastr.js') }}"></script>
<!-- GLOBAL -->
<script src="{{ asset(Utility::THEME_ADMIN . 'bootstrap/js/global.js') }}"></script>
<!--// GLOBAL //-->

<!-- VALIDATOR -->
<script src="{{ asset(Utility::THEME_ADMIN . 'vendor/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset(Utility::THEME_ADMIN . 'vendor/jquery-validation/additional-methods.min.js') }}"></script>
<!--// VALIDATOR -->

<script>
    $(document).ready(function() {
        @include('admin.partials.site-alert')
     });
</script>

<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<!-- Bootstrap 3.3.6 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'bootstrap/js/bootstrap.min.js') }}"></script>


<!-- Select2 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/select2/select2.full.min.js') }}"></script>
<!-- iCheck 1.0.1 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/iCheck/icheck.min.js') }}"></script>

<!-- SlimScroll -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/fastclick/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset(Utility::THEME_ADMIN . 'dist/js/app.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset(Utility::THEME_ADMIN . 'dist/js/demo.js') }}"></script>

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
        //Initialize Select2 Elements
        $(".select2").select2();
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });

    });
</script>
{{--<script>
        $(document).ready(function () {
            toastr.success('Success Message', null, {
                containerId:"toast-topFullWidth",
                positionClass:"toast-top-full-width",
                showMethod:"slideDown",
                closeButton: true
            });
        });
    </script>--}}

@stack('page_scripts')
