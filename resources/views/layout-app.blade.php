<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @yield('title')
    <link rel="shortcut icon" href="">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="js_local" content="/{{ App::getLocale() }}">

    <!-- All Plugins Css -->
    <link rel="stylesheet" href="/site/css/plugins.css">
    <link rel="stylesheet" href="/site/css/nav.css" />

    <!-- style-rtl -->
    @if(App::getLocale()=="ar")
        <link href="/site/css/bootstrap-rtl.min.css" rel="stylesheet">
@endif
    <!-- Custom CSS -->
    <link href="/site/css/styles.css" rel="stylesheet">
    @if(App::getLocale()=="ar")
        <link href="/site/css/style-rtl.css" rel="stylesheet">
@endif
    <link href="/site/css/common.css" rel="stylesheet">

    <link rel="icon" href="/site/images/logo.png">


    <!--[if lt IE 9]>
    <script src="/site/js/html5shiv.min.js"></script>
    <script src="/site/js/respond.min.js"></script>
    <![endif]-->
</head>
<body class="green-skin">
<!-- loader -->
<div id="preloader">
    <div class="preloader"><span></span><span></span></div>
</div>

@include('alert')
<div id="main-wrapper">

    <!-- ============================================================== -->
    <!-- Header-->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Top header  -->
    <!-- ============================================================== -->
@yield('content')

</div>
<!-- End Wrapper -->
<!-- All Jquery -->
<script src="/site/js/jquery.min.js"></script>
<script src="/site/js/popper.min.js"></script>
<script src="/site/js/bootstrap.min.js"></script>
<script src="/site/js/select2.min.js"></script>
<script src="/site/js/owl.carousel.min.js"></script>
<script src="/site/js/jquery.magnific-popup.min.js"></script>
<script src="/site/js/slick.js"></script>
<script src="/site/js/slider-bg.js"></script>
<script src="/site/js/coreNavigation.js"></script>
<script src="/site/js/custom.js"></script>

@yield('js')
</body>
</html>