@include('admin.includes.head')

  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/iCheck/flat/blue.css') }}">
  <!-- Morris chart -->
  {{--<link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/morris/morris.css') }}">--}}
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
  <!-- Date Picker -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/datepicker/datepicker3.css') }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/daterangepicker/daterangepicker.css') }}">
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 @include('admin.includes.header')

 @include('admin.includes.sidemenu')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" >

            @yield('content')

  </div>
  <!-- /.content-wrapper -->

    <footer class="main-footer" >
        @include('admin.includes.footer')
    </footer>

</div>
<!-- ./wrapper -->
@include('admin.includes.foot')

<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
{{--<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/morris/morris.min.js') }}"></script>--}}
<!-- Sparkline -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/knob/jquery.knob.js') }}"></script>
<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>

</body>
</html>
@stack('post_body')
