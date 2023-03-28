<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $contacts = \App\Models\Settings::select('option_name', 'name', 'value')->where('input_type', 'contact_options')
           ->orWhere('input_type', 'app_links')
                   ->orWhere('input_type', 'social_media')
                  ->orWhere('input_type','meta_data')
           ->get();
   $contact_phone='';
   $contact_whatsapp='';
   $contact_email='';
   $contact_address='';
   $facebook='';
   $instagram='';
   $youtube='';
   $twitter='';
   $site_title='';
   $site_description='';
   $site_keywords='';
   foreach ($contacts as $contact){
       if($contact->option_name=='phone')$contact_phone=$contact->value;
           if($contact->option_name=='whatsapp')$contact_whatsapp=$contact->value;
           if($contact->option_name=='email')$contact_email=$contact->value;
           if($contact->option_name=='address')$contact_address=$contact->value;
           if($contact->option_name=='facebook')$facebook=$contact->value;
           if($contact->option_name=='instagram')$instagram=$contact->value;
           if($contact->option_name=='youtube')$youtube=$contact->value;
           if($contact->option_name=='twitter')$twitter=$contact->value;
           if($contact->option_name=='site_title')$site_title=$contact->value;
           if($contact->option_name=='site_description')$site_description=$contact->value;
           if($contact->option_name=='site_keywords')$site_keywords=$contact->value;

   }
    @endphp
    <link rel="shortcut icon" href="/site/imgs/fav-i.png" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="DoBa Nabil"/>
    <meta name="description" content="{{$site_description}}">
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="{{$site_title}}"/>
    <meta property="og:description" content="{{$site_description}}"/>
    <meta property="og:url" content="https://entlq.com/"/>
    <meta property="og:site_name" content="مؤسسة الطريق الذهبي"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @yield('title')
    <link rel="shortcut icon" href="">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="js_local" content="/{{ App::getLocale() }}">

    <!-- style -->
    <link href="/site/css/bootstrap.min.css" rel="stylesheet">
    <link href="/site/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/site/css/line-awesome.css">
    <link href="/site/css/wenk.min.css" rel="stylesheet">
    <link href="/site/css/hover-min.css" rel="stylesheet">
    <link href="/site/css/owl.carousel.min.css" rel="stylesheet">
    <link href="/site/css/owl.theme.default.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/site/css/star-rating.min.css" />
    <link rel="stylesheet" href="/site/css/lightgallery.min.css"/>
    <link href="/site/css/select2.min.css" rel="stylesheet" />
    <link href="/site/css/fotorama.css" rel="stylesheet">
    <link href="/site/css/dataTables.min.css" rel="stylesheet">
    <link href="/site/css/style.css" rel="stylesheet">
    <!--if lang == en-->
    <!--<link href="css/style-en.css" rel="stylesheet">-->
    <!---->
    <link href="/site/css/responsive.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/site/js/html5shiv.min.js"></script>
    <script src="/site/js/respond.min.js"></script>

    <![endif]-->

    <style>
        body{
            overflow-x: hidden;
        }
    </style>
</head>
<body class="green-skin" >
<!-- loader -->
<!-- preloader area start -->
{{--<div id="preloader">--}}
{{--    <div class="spinner"></div>--}}
{{--</div>--}}
<!-- preloader area end -->
@include('alert')
@php
    if(auth()->check()){
        $tax=floatval(\App\Models\Settings::find(38)->value);
                    $items=\App\Models\CartItem::where('type',1)->where(['status'=>0,'order_id'=>0])->whereHas('product')
                    ->whereHas('product')
                    ->with(['product' => function ($query) use($tax){
                        $query->select('id','title','photo','min_quantity','quantity',DB::raw('ROUND((price +(price * '.($tax/100).')),2) as price'));
                       $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo');
                    }])
                    ->where('user_id',auth()->id())->get();
    }

@endphp
<a id="button">
    <i class="las la-arrow-circle-up"></i>
</a>
<!-- start header section -->
<!--<span data-wenk="👉 I'm to the right!" data-wenk-pos="right">Wenk to the right!</span>-->
<div id="app">
    @if(auth()->check() && auth()->user()->approved==0)
    <div class=" py-2"  style="background: #f9f2f4">
        <div class="container text-center">
             <span>
                الحساب قيد المراجعة الان
           </span>
        </div>
    </div>
    @endif

    @if(auth()->check() && auth()->user()->activate==2)
        <div class=" py-2"  style="background: #f9f2f4">
            <div class="container text-center">
         <span>
            {{auth()->user()->cancel_reason}}
       </span>
            </div>
        </div>
    @endif

    <section class="header">
            <!--top header-->
            <div class="top-header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-4">
                            <ul class="social-icons">
                                <li>
                                    <a title="واتس اب" href="https://api.whatsapp.com/send?phone={{$contact_whatsapp}}">
                                        <i class="lab la-whatsapp"></i>
                                    </a>
                                </li>
{{--                                <li>--}}
{{--                                    <a title="المقارنات" href="#">--}}
{{--                                        <i class="lar la-chart-bar"></i>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                        <div class="col-md-4 col-2 text-center">
                            <a style="vertical-align: middle" href="#">
                                <img src="/site/imgs/Logo_Icon.svg">
                            </a>
                        </div>
                        <div class="col-md-4 col-6" style="text-align: left;">
                            <a href="mailto:({{$contact_email}})" style="color: #fff;">
                                <i class="las la-envelope"></i>
                                <span>{{$contact_email}} </span></a>
                            {{--                    <div class="dropdown">--}}
        {{--                        <a style="padding-top: 0;padding-bottom: 0" class="btn" type="button" id="dropdownMenuButton1"--}}
        {{--                           data-bs-toggle="dropdown" aria-expanded="false">--}}
        {{--                            العربية--}}
        {{--                            <i class="las la-angle-down"></i>--}}
        {{--                        </a>--}}
        {{--                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">--}}
        {{--                            <li><a class="dropdown-item" href="#">العربية</a></li>--}}
        {{--                            <li><a class="dropdown-item" href="#">English</a></li>--}}
        {{--                        </ul>--}}
        {{--                    </div>--}}
                        </div>
                    </div>
                </div>
            </div>
            <!--center header-->
            <div class="center-header">
                <div class="container">
                    <div class="row">
                        <div class="col-md-5 col-1">
                            <a class="logo" href="/">
                                <img alt="logo" src="/images/logo.png">
                            </a>
                        </div>
                        <div class="col-md-7 col-11">
                            <ul class="user-tabs">
                                @if(auth()->user())

                                <li>
                                    <div class="dropdown">
                                        <a class="btn" type="button" id="profile" data-bs-toggle="dropdown"
                                           aria-expanded="false">
                                            <div>
                                                <i class="la la-user"></i>
                                            </div>
                                            <span>حسابي</span>
                                        </a>
                                        <ul class="profile-menu dropdown-menu" aria-labelledby="profile">
                                            <li class="credit_list px-1">
                                                <b>رصيدي : </b>
                                                <p>{{@round(\App\Models\Balance::where('user_id',auth()->id())->sum('price'),2)}} SAR</p>
                                            </li>
                                            <li><a class="dropdown-item" href="/my-orders">طلباتي </a></li>
                                            <li><a class="dropdown-item" href="/addresses">قائمة عناوينى</a></li>
                                            <li><a class="dropdown-item" href="{{route('account')}}">تعديل الحساب</a></li>
                                            <div class="buttons">
                                                <a href="/logout" class="button hvr-sweep-to-left" style="width: 100%;">
                                                <span>
                                                    تسجيل خروج
                                                </span>
                                                </a>
                                            </div>
                                        </ul>

                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown">
                                        <a class="btn" href="/favorites">
                                            <div>
                                                <i class="la la-heart"></i>
                                            </div>
                                            <span>المفضلة</span>
                                        </a>
                                    </div>
                                </li>
                                <li >
                                            <cart :items="{{$items}}" />
                                </li>
                                @else
                                    <li>
                                        <div class="dropdown">
                                            <a class="btn" href="/login">
                                                <div>
                                                    <i class="la la-user"></i>
                                                </div>
                                                <span>تسجيل دخول</span>
                                            </a>
                                        </div>
                                    </li>
        {{--                            <li>--}}
        {{--                                <div class="dropdown">--}}
        {{--                                    <a class="btn" href="/register">--}}
        {{--                                        <div>--}}
        {{--                                            <i class="las la-user-plus"></i>--}}
        {{--                                        </div>--}}
        {{--                                        <span>انشاء حساب</span>--}}
        {{--                                    </a>--}}
        {{--                                </div>--}}
        {{--                            </li>--}}

                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- bottom header -->
            <div class="bottom-header">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <nav class="navbar navbar-expand-lg navbar-light">
                                <div class="container-fluid">
                                    <div class="nav-item dropdown custom-menu">
                                        <a class="nav-link" href="#" data-bs-toggle="dropdown">
                                            <img src="/site/imgs/Menu.svg" alt="الطريق-الذهبي">
                                            <i class="las la-times"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            @foreach(\App\Models\Categories::where('stop',0)->where('is_archived',0)->orderBy('sort','asc')->get() as $category)
                                            <li class="has-megasubmenu">
                                                <a class="dropdown-item" href="/search?page=1&category_id=[{{@$category->id}}]">
                                                    {{@$category->name}}
                                                    <i class="las la-angle-left"></i>
                                                </a>
                                            </li>
                                            @endforeach

        {{--                                    <li class="has-megasubmenu">--}}
        {{--                                        <a class="dropdown-item" href="#">--}}
        {{--                                            اسم القائمة--}}
        {{--                                            <i class="las la-angle-left"></i>--}}
        {{--                                        </a>--}}
        {{--                                        <div class="megasubmenu dropdown-menu">--}}
        {{--                                            <div class="row">--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                                <div class="col-6 col-md-3">--}}
        {{--                                                    <h6 class="title">--}}
        {{--                                                        <a href="#">اسم التصنيف</a>--}}
        {{--                                                    </h6>--}}
        {{--                                                    <ul class="list-unstyled">--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                        <li><a href="#">اسم المنتج</a></li>--}}
        {{--                                                    </ul>--}}
        {{--                                                </div><!-- end col-3 -->--}}
        {{--                                            </div><!-- end row -->--}}
        {{--                                        </div>--}}
        {{--                                    </li>--}}
                                        </ul>
                                    </div>
                                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                            aria-expanded="false" aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                            <li class="nav-item">
                                                <a class="nav-link active" aria-current="page" href="/">الرئيسية</a>
                                            </li>
@if(!auth()->check())
                                            <li class="nav-item">
                                                <a class="nav-link"  href="/become-provider">انضم كمورد</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="/login">انضم كمحل </a>
                                            </li>
                                            @endif
                                            <li class="nav-item">
                                                <a class="nav-link" href="/providers">قائمة الموردين </a>
                                            </li>

                                            <li class="nav-item">
                                                <a class="nav-link" href="/page/about-us">من نحن </a>
                                            </li>
                                            {{--
                                                                                        <li class="nav-item">
                                                                                            <a class="nav-link" href="#">قائمة الموردين </a>
                                                                                        </li>
                                            {{--                                            <li class="nav-item">--}}
{{--                                                <a class="nav-link" href="#"> دخول موردين </a>--}}
{{--                                            </li>--}}

                                        </ul>
                                        <form class="search-box" method="get" action="/search">
                                            <input autocomplete="off" name="search" id="search_box" type="text" placeholder="بحث باسم المنتج" class="search-input">
                                            <button  class="search-btn">
                                                <i class="las la-search"></i>
                                            </button>

                                            <div id="searchList">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- end header section -->
        @yield('content')

        <!-- start footer section -->
        <div>
            <div class="text-center">
                <div>
                    <a class="d-inline-block" href="{{\App\Models\Settings::find(27)->value}}">
                        <img src="/images/google-play.png" >
                    </a>
                    <a class="d-inline-block" href="{{\App\Models\Settings::find(28)->value}}">
                        <img src="/images/app-store.png" width="135">
                    </a>
                </div>
            </div>
        </div>
        <footer class="site-footer p-0">
{{--    <div class="container">--}}
{{--        <div class="row">--}}
{{--            <div class="col-sm-12 col-md-4 ">--}}
{{--                <div class="footer-info">--}}
{{--                    <a class="footer-logo" href="#">--}}
{{--                        <img src="/site/imgs/Logo.svg" alt="footer logo">--}}
{{--                    </a>--}}
{{--                    <p class="text-justify">--}}
{{--                        {{$contact_address}}--}}
{{--                    </p>--}}
{{--                    <p style="direction: ltr" class="text-justify">--}}
{{--                        <a title="اتصل بنا" href="mailto:({{$contact_email}})" >--}}
{{--                            <span style="color: #0a001f;">{{$contact_email}} </span>--}}
{{--                            <i class="las la-envelope"></i>--}}
{{--                        </a>--}}
{{--                    </p>--}}

{{--                    <p style="direction: ltr" class="text-justify">--}}
{{--                        <a title="واتس اب" href="https://api.whatsapp.com/send?phone={{$contact_whatsapp}}">--}}
{{--                            <span style="color: #0a001f">{{$contact_whatsapp}}</span>--}}
{{--                            <i class="lab la-whatsapp"></i>--}}
{{--                        </a>--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}


{{--            <div class="col-4 col-md-2">--}}
{{--                <h6>الطريق الذهبي</h6>--}}
{{--                <ul class="footer-links">--}}
{{--                    <li><a href="#">عنا</a></li>--}}
{{--                    <li><a href="#">اتصل بنا</a></li>--}}
{{--                    <li><a href="#">الشروط & الأحكام</a></li>--}}
{{--                    <li><a href="#">سياسة الخصوصية</a></li>--}}
{{--                    <li><a href="#">الموقع</a></li>--}}
{{--                </ul>--}}
{{--            </div>--}}

{{--            <div class="col-4 col-md-2">--}}
{{--                <h6>الطريق الذهبي</h6>--}}
{{--                <ul class="footer-links">--}}
{{--                    <li><a href="#">اضم كمورد</a></li>--}}
{{--                    <li><a href="#">انضم كتاجر</a></li>--}}
{{--                    <li><a href="#">تسجيل الدخول</a></li>--}}
{{--                    <li><a href="#">متابعة طلباتك</a></li>--}}
{{--                    <li><a href="#">المفضلة</a></li>--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--            <div class="col-4 col-md-2">--}}
{{--                <h6>حسابي</h6>--}}
{{--                <ul class="footer-links">--}}
{{--                    <li><a href="#">الدخول</a></li>--}}
{{--                    <li><a href="#">انشاء حساب</a></li>--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="terms">
        <div class="container">
            <div class="row">

                <div class="col-12">
                    <a href="/page/about-us">
                        عن الطريق الذهبي
                    </a>
                    <a href="/page/conditions">
                        الشروط والأحكام
                    </a>
                    <a href="/page/privacy">
                        سياسة الخصوصية
                    </a>
                    <a href="/contact-us">
                        اتصل بنا
                    </a>

                    <div class="social">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <ul class="social-icons">
                                        @if($facebook)
                                            <li><a class="facebook" href="{{$facebook}}"><i class="lab la-facebook-f"></i></a></li>
                                        @endif
                                        @if($twitter)
                                            <li><a class="twitter" href="{{$twitter}}"><i class="lab la-twitter"></i></a></li>
                                        @endif
                                        @if($youtube)
                                            <li><a class="youtube" href="{{$youtube}}">
                                                    <i class="lab la-youtube"></i>
                                                </a></li>
                                        @endif
                                        @if($instagram)
                                            <li><a class="instagram" href="{{$instagram}}"><i class="lab la-instagram"></i></a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <span style="width: 100%;text-align: center;">
                       جميع الحقوق محفوظة لشركة الطريق الذهبي  &copy; {{ date('Y') }}
                    </span>
                </div>
                </div>
        </div>
    </div>

    <div>
        <confirmationbox />
    </div>
</footer>


</div>
<!-- end footer section -->
<!--js-->
    {{--    <script src="{{ asset('js/app.js') }}" defer></script>--}}


    <script src="{{ mix('js/app.js') }}"></script>
<script src="/site/js/jquery-3.6.0.min.js"></script>
<script src="/site/js/jquery.slimscroll.min.js"></script>
<script src="/site/js/select2.min.js"></script>
<script src="/site/js/popper.min.js"></script>
<script src="/site/js/bootstrap.min.js"></script>
<script src="/site/js/owl.carousel.min.js"></script>
<script src="/site/js/owlcarousel-filter.min.js"></script>
<script src="/site/js/star-rating.min.js"></script>
<script src="/site/js/fotorama.js"></script>
{{--<script src="/site/js/picturefill.min.js"></script>--}}
<script src="/site/js/lightgallery.min.js"></script>
<script src="/site/js/lg-pager.min.js"></script>
<script src="/site/js/lg-autoplay.min.js"></script>
<script src="/site/js/lg-fullscreen.min.js"></script>
<script src="/site/js/lg-zoom.min.js"></script>
<script src="/site/js/lg-hash.min.js"></script>
<script src="/site/js/lg-share.min.js"></script>
<script src="/site/js/lg-rotate.min.js"></script>
<script src="/site/js/lg-video.min.js"></script>
<script src="/site/js/jquery.dataTables.min.js"></script>
<script src="/site/js/dataTables.bootstrap5.min.js"></script>
<!--if lang == en-->
<!--<script src="js/main-en.js"></script>-->
<!--if lang == ar-->
<script src="/site/js/main.js"></script>
<!---->
<script>
    $(document).ready(function(){
        if(localStorage.getItem('popup')==true){
            $('.install-app').show()
        }
        $('.close-pop-up').on('click',function(e){
            e.preventDefault()
            localStorage.setItem('popup',false)
            $('.install-app').remove()
        })
        $('#search_box').on('keyup',function(){
            var query = $(this).val();
            if(query != '')
            {
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url:"{{ route('autocomplete.fetch') }}",
                    method:"POST",
                    data:{query:query, _token:_token},
                    success:function(data){
                        $('#searchList').fadeIn();
                        $('#searchList').html(data);
                    }
                });
            }
        });

     /*   $(document).on('click', 'li', function(){
            alert('')
            // $('#country_name').val($(this).text());
            // $('#countryList').fadeOut();
        });*/

    });
</script>
@yield('js')

<!---->
</body>
</html>
