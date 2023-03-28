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

    <link rel="stylesheet" type="text/css" href="/site/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/site/css/swiper.min.css">

    <link rel="stylesheet" type="text/css" href="/site/css/style.css">
    <link rel="stylesheet" type="text/css" href="/site/css/responsive.css">


    <!--[if lt IE 9]>
    <script src="/site/js/html5shiv.min.js"></script>
    <script src="/site/js/respond.min.js"></script>
    <![endif]-->

</head>
<body data-spy="scroll" data-target=".navbar" data-offset="50">
@include('alert')
<!-- start navbar-->
<nav class="navbar navbar-expand-lg fixed-top navbar-light">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/site/images/logo.svg">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse " id="navbarsExample07">
            <ul class="navbar-nav  ">
                <li class="nav-item  pr-5">
                    <a class="nav-link active" href="/#about-app">عن التطبيق <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/#features">المميزات</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="/#app-screens">واجهات</a>
                </li>
                {{--<li class="nav-item">--}}
                    {{--<a class="nav-link " href="register.html"> سجل كمندوب</a>--}}
                {{--</li>--}}

                <li class="nav-item">
                    <a class="nav-link " href="/#call-us">إتصل بنا</a>
                </li>


            </ul>


        </div>
        <div class="d-flex justify-content-end d-md-none d-sm-none">
            <ul class="navbar-nav  ">
                <li class="nav-item">
                    <a class="nav-link btn btn-success" href="/#call-us">حمل التطبيق</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!--end navbar-->



@yield('content')
<!--footer-->
<footer id="call-us" class="py-md-4">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div>
                    <img src="/site/images/logo_icon.svg">
                </div>
            </div>
            <div class="col-md-6 d-flex justify-content-center align-items-center">
                <div >
                    <ul class=" m-0 links p-0">
                        <li>
                            <a href="#about-app">عن التطبيق</a>
                        </li>
                        <li>
                            <a href="#features">المميزات</a>
                        </li>
                        <li>
                            <a href="#app-screens">واجهات</a>
                        </li>

                        <li>
                            <a href="#call-us">اتصل بنا</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-center align-items-center">
                <div class="text-left">
                    <ul class="  m-0  ">
                        <li class="d-inline-block"><a href="{{ \App\Models\Settings::find(5)->value }}"><img src="/site/images/face.png" ></a></li>
                        <li class="d-inline-block"><a href="{{ \App\Models\Settings::find(6)->value }}"><img src="/site/images/youtube.svg"></a></li>
                        <li class="d-inline-block"><a href="{{ \App\Models\Settings::find(7)->value }}"><img src="/site/images/insta.png"></a></li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</footer>


<script src="/site/js/jquery-3.2.1.min.js"></script>
<script src="/site/js/bootstrap.js"></script>
<script src="/site/js/swiper.min.js"></script>
<script src="/site/js/js.js"></script><!--Your jquery file-->
@yield('js')
</body>
</html>