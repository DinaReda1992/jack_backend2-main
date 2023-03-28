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

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- CSS -->
    <link rel="stylesheet" href="/site/css/bootstrap.min.css">

    <link rel="stylesheet" href="/site/css/bootstrap-rtl.min.css">

    <link rel="stylesheet" href="/site/css/jquery-ui.css">
    <link rel="stylesheet" href="/site/css/font-awesome.min.css">
    <link rel="stylesheet" href="/site/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/site/css/slicknav.min.css">
    <link rel="stylesheet" href="/site/css/magnificpopup.css">
    <link rel="stylesheet" href="/site/css/jquery.mb.YTPlayer.min.css">
    <link rel="stylesheet" href="/site/css/typography.css">
    <link rel="stylesheet" href="/site/css/style.css">
    <link rel="stylesheet" href="/site/css/style-rtl.css">

    <link rel="stylesheet" href="/site/css/responsive.css">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="/site/img/icon/favicon.ico">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="green-skin">
<!-- loader -->
<!-- preloader area start -->
{{--<div id="preloader">--}}
{{--    <div class="spinner"></div>--}}
{{--</div>--}}
<!-- preloader area end -->
@include('alert')

<!-- header area start -->
<header id="header">
    <div class="header-area">
        <div class="container">
            <div class="menu-area">
                <div class="row">
                    <div class="col-md-2 col-sm-12 col-xs-12">
                        <div class="logo">
                            <a href=""><img src="/images/logo.png" alt="To Cars"></a>
                        </div>
                    </div>
                    <div class="col-md-10 hidden-xs hidden-sm">
                        <div class="main-menu">
                            <nav class="nav-menu">
                                <ul>
                                    <li class="active"><a href="/#home">الرئيسية</a></li>
                                    <li><a href="/#about">عن التطبيق</a></li>

                                    <li><a href="/#feature">المميزات</a></li>
                                    <li><a href="/#screenshot">عرض التطبيق</a></li>
                                    <li><a href="/#contact">اتصل بنا</a></li>
                                    <li><a href="/page/conditions">الشروط والأحكام</a></li>
                                    <li><a href="/page/privacy">سياسة الخصوصية</a></li>

                                    <li><a href="/page/retrieval">سياسة الاسترجاع</a></li>
                                    <li><a href="/page/delivery-policy">سياسة الشحن والتوصيل</a></li>

                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12 visible-sm visible-xs">
                        <div class="mobile_menu"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- header area end -->
@yield('content')

<!-- footer area start -->
<footer>
    <div style="text-align: center;">
        <img src="/images/visa.png" style="width: 78px">
        <img src="/images/Master-Card.png" style="width: 64px">

    </div>
    <div class="footer-area">
        <div class="container">
            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | Designed   <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://entlq.com" target="_blank" style="color: #c50018">Entlq</a>
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
        </div>
    </div>
</footer>
<!-- footer area end -->
<!-- End Wrapper -->
<!-- All Jquery -->
<script src="/site/js/jquery-3.2.0.min.js"></script>
<script src="/site/js/jquery-ui.js"></script>
<script src="/site/js/bootstrap.min.js"></script>

<script src="/site/js/jquery.slicknav.min.js"></script>
<script src="/site/js/owl.carousel.min.js"></script>
<script src="/site/js/magnific-popup.min.js"></script>
<script src="/site/js/counterup.js"></script>
<script src="/site/js/jquery.waypoints.min.js"></script>
<script src="/site/js/jquery.mb.YTPlayer.min.js"></script>
<script src="/site/js/warm-canvas.js"></script>
<script src="/site/js/theme.js"></script>


@yield('js')
</body>
</html>