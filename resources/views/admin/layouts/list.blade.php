@include('admin.includes.head')

  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset(Utility::THEME_ADMIN . 'plugins/datatables/dataTables.bootstrap.css') }}">
  {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">--}}
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 @include('admin.includes.header')

 @include('admin.includes.sidemenu')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

            @yield('content')

  </div>
  <!-- /.content-wrapper -->

    <footer class="main-footer">
        @include('admin.includes.footer')
    </footer>

</div>
<!-- ./wrapper -->

@include('admin.includes.foot')
        <!-- DataTables -->
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/datatables/jquery.dataTables.min.js') }}"></script>
{{--<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>--}}
<script src="{{ asset(Utility::THEME_ADMIN . 'plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
</body>
</html>
@stack('post_body')
