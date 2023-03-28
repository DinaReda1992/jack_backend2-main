<!doctype html>
<html lang="{{ __('dashboard.lang') }}" dir="{{ __('dashboard.dir') }}">
@php
    $contacts = \App\Models\Settings::select('option_name', 'name', 'value')
        ->where('input_type', 'contact_options')
        ->orWhere('input_type', 'app_links')
        ->orWhere('input_type', 'social_media')
        ->orWhere('input_type', 'meta_data')
        ->get();
    $contact_phone = '';
    $contact_whatsapp = '';
    $contact_email = '';
    $contact_address = '';
    $facebook = '';
    $instagram = '';
    $youtube = '';
    $twitter = '';
    $site_title = '';
    $site_description = '';
    $site_keywords = '';
    foreach ($contacts as $contact) {
        if ($contact->option_name == 'phone') {
            $contact_phone = $contact->value;
        }
        if ($contact->option_name == 'whatsapp') {
            $contact_whatsapp = $contact->value;
        }
        if ($contact->option_name == 'email') {
            $contact_email = $contact->value;
        }
        if ($contact->option_name == 'address') {
            $contact_address = $contact->value;
        }
        if ($contact->option_name == 'facebook') {
            $facebook = $contact->value;
        }
        if ($contact->option_name == 'instagram') {
            $instagram = $contact->value;
        }
        if ($contact->option_name == 'youtube') {
            $youtube = $contact->value;
        }
        if ($contact->option_name == 'twitter') {
            $twitter = $contact->value;
        }
        if ($contact->option_name == 'site_title') {
            $site_title = $contact->value;
        }
        if ($contact->option_name == 'site_description') {
            $site_description = $contact->value;
        }
        if ($contact->option_name == 'site_keywords') {
            $site_keywords = $contact->value;
        }
    }
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $site_description }}">
    <meta name="author" content="{{ $site_title }}">
    <meta name="generator" content="{{ $site_title }}">
    @yield('title')
    <link rel="stylesheet" href="/asset/fonts/_HelveticaNeueLT B.otf">
    <link rel="stylesheet" href="/asset/fonts/_HelveticaNeueLT.otf">
    <link rel="stylesheet" href="/asset/fonts/HelveticaNeueLT B.otf">
    <link rel="stylesheet" href="/asset/fonts/HelveticaNeueLT.otf">
    <link rel="stylesheet" href="/asset/vendors/fontawesome-pro-5/css/all.css">
    <link rel="stylesheet" href="/asset/vendors/bootstrap-select/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="/asset/vendors/slick/slick.min.css">
    <link rel="stylesheet" href="/asset/vendors/magnific-popup/magnific-popup.min.css">
    <link rel="stylesheet" href="/asset/vendors/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="/asset/vendors/animate.css">
    {{-- <link rel="stylesheet" href="/asset/vendors/mapbox-gl/mapbox-gl.min.css"> --}}
    <link rel="stylesheet" href="/asset/vendors/fonts/font-phosphor/css/phosphor.min.css">
    <link rel="stylesheet" href="/asset/vendors/fonts/tuesday-night/stylesheet.min.css">
    <link rel="stylesheet" href="/asset/vendors/fonts/butler/stylesheet.min.css">
    <link rel="stylesheet" href="/asset/vendors/fonts/a-antara-distance/stylesheet.min.css">
    <!-- Themes core CSS -->
    <!--<link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.5.3/css/bootstrap.min.css" integrity="sha384-JvExCACAZcHNJEc7156QaHXTnQL3hQBixvj5RV5buE7vgnNEzzskDtx9NQ4p6BJe" crossorigin="anonymous">-->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="https://cdn.salla.network/fonts/pingarlt.css?v=1.0">
        <link rel="stylesheet" href="https://cdn.salla.network/fonts/sallaicons.css">
        <link rel="pingback" href="https://salla.com/xmlrpc.php">
       
       
        <link rel="stylesheet" href="/asset/css/themes.css">
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="/asset/css/rtl.css">
    @endif
    <!-- Favicons -->
    <link rel="icon" href="/images/logoicon.png">
    <!-- Twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@">
    <meta name="twitter:creator" content="@">
    <meta name="twitter:title" content="{{ $site_title }}">
    <meta name="twitter:description" content="{{ $site_description }}">
    <meta name="twitter:image" content="/images/logoicon.png">
    <!-- Facebook -->
    <meta name="description" content="{{ $site_description }}">
    <meta property="og:url" content="https://entlq.com/">
    <meta property="og:title" content="{{ $site_title }}" />
    <meta property="og:description" content="{{ $site_description }}" />
    <meta property="og:type" content="website">
    <meta property="og:image" content="/images/logoicon.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="Jak" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="js_local" content="/{{ App::getLocale() }}">
    <style>
        .v-toast__item .v-toast__icon {
            margin-right: 1em;
        }
    </style>
</head>

<body>
    {{-- <div id="spinner-container">
        <div id="loading-spinner"></div>
    </div> --}}
    <div id="app">
        <header id="mainheader" class="main-header navbar-light header-sticky header-sticky-smart header-03">
            @php
                $top_title = app()->getLocale() == 'ar' ? App\Models\Settings::find(51)->value : App\Models\Settings::find(53)->value;
            @endphp
            @if ($top_title)
                <div class="topbar" style="background-color: #4e0161">
                    <div class="container container-xl  mobile-div">
                        <a href="{{ App\Models\Settings::find(54)->value }}">
                            <p
                                class="mb-0 fs-15 font-weight-bold text-secondary text-center letter-spacing-01 text-uppercase">
                                {{ $top_title }}</p>
                        </a>
                        <ul class="languages full-menu mobile-lang">
                                    @if (app()->getLocale() != 'en')
                                        <li class="languages__item">
                                            <a class="languages__link {{ app()->getLocale() == 'en' ? 'active' : 'active' }}"
                                                rel="alternate" hreflang="{{ 'en' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                                EN
                                            </a>
                                        </li>
                                    @else
                                        <li class="languages__item">
                                            <a class="languages__link {{ app()->getLocale() == 'ar' ? 'active' : 'active' }}"
                                                rel="alternate" hreflang="{{ 'ar' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                                ع
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                    </div>
                </div>
            @endif
            <div class="header-above d-none d-xl-block mb-n4">
                <div class="container container-xl">
                    <div class="d-flex align-items-center flex-nowrap">
                        <div class="w-50">
                            {{-- <a class="px-4" href="#search-popup" data-gtf-mfp="true"
                                data-mfp-options='{"type":"inline","focus": "#keyword","mainClass": "mfp-search-form mfp-move-from-top mfp-align-top"}'
                                class="nav-search d-flex align-items-center">
                                <svg class="icon icon-magnifying-glass-light fs-28">
                                    <use xlink:href="#icon-magnifying-glass-light">
                                        </us>
                                </svg>
                            </a> --}}
                            {{-- <span class="d-none d-xl-inline-block ml-2 font-weight-500">ا{{__('dashboard.search')}}</span> --}}
                            <form class="d-flex align-items-center h-100" method="get" action="/search">
                                <div class="input-group position-relative mw-270 mr-auto">
                                    <input autocomplete="off" name="keyword" id="search_box" type="text"
                                        class="form-control form-control bg-transparent"
                                        placeholder="{{ __('dashboard.search') }}" style="border-radius: 50px;">
                                    <div class="input-group-append position-absolute pos-fixed-right-center">
                                        <button class="input-group-text bg-transparent border-0 px-0 fs-28 pr-3" aria-label="search"
                                            type="submit">
                                            <i class="fal fa-search fs-20 font-weight-normal"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div id="searchList">
                            </div>
                        </div>
                        <div class="mx-auto flex-shrink-0 px-10 py-6">
                            <div class="d-flex mt-3 mt-xl-0 align-items-center w-100 justify-content-center">
                                <a class="navbar-brand mx-auto d-inline-block py-0" href="/">
                                    <img src="/images/logo.png" alt="Jak">
                                </a>
                            </div>
                        </div>
                        <div class="w-50">
                            <div class="d-flex align-items-center justify-content-end">
                                <ul class="languages full-menu">
                                    @if (app()->getLocale() != 'en')
                                        <li class="languages__item">
                                            <a class="languages__link {{ app()->getLocale() == 'en' ? 'active' : 'active' }}"
                                                rel="alternate" hreflang="{{ 'en' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                                EN
                                            </a>
                                        </li>
                                    @else
                                        <li class="languages__item">
                                            <a class="languages__link {{ app()->getLocale() == 'ar' ? 'active' : 'active' }}"
                                                rel="alternate" hreflang="{{ 'ar' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                                ع
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                <layout-labels :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}"
                                    :statistic_data='{{ json_encode($statistic_data) }}'>
                                </layout-labels>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky-area">
                <div class="container container-xl">
                    <nav class="d-flex justify-content-between navbar navbar-expand-xl px-0 d-block">
                        <!-- <div class="smallnav">
                            <div class="d-flex align-items-center justify-content-center">
                                <layout-labels :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}"
                                    :statistic_data='{{ json_encode($statistic_data) }}'>
                                </layout-labels>
                            </div>
                        </div> -->

                        <div class="logo-sticky">
                            <div class="d-flex mt-3 mt-xl-0 align-items-center w-100 justify-content-center">

                                <a class="navbar-brand mx-auto d-inline-block py-0" href="/">
                                    <img src="/images/logo.png" alt="Jak">
                                </a>
                                <a href="#search-popup" data-gtf-mfp="true"
                                    data-mfp-options='{"type":"inline","focus": "#keyword","mainClass": "mfp-search-form mfp-move-from-top mfp-align-top"}'
                                    class="nav-search d-flex align-items-center">
                                    <svg class="icon icon-magnifying-glass-light fs-28">
                                        <use xlink:href="#icon-magnifying-glass-light"></use>
                                    </svg>
                                    <!--<span class="d-none d-xl-inline-block ml-2 font-weight-500">{{ __('dashboard.search') }}</span>-->
                                </a>
                            </div>
                        </div>
                        <div class="links-sticky d-none d-xl-flex justify-content-xl-center menu-01">

                            <ul class="navbar-nav hover-menu main-menu px-0 mx-xl-n5">
                                <li aria-haspopup="true" aria-expanded="false"
                                    class="nav-item py-2 py-xl-7 sticky-py-xl-6 px-0 px-xl-5">
                                    <a class="nav-link p-0" href="/">
                                        {{ __('dashboard.home') }}
                                    </a>
                                </li>
                                <li aria-haspopup="true" aria-expanded="false"
                                    class="nav-item dropdown-item-shop dropdown py-2 py-xl-7 sticky-py-xl-6 px-0 px-xl-5">
                                    <a class="p-0 nav-link dropdown-toggle" href="/search">
                                        {{ __('dashboard.products') }}
                                        <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-xl px-0 pb-10 pt-5 dropdown-menu-listing overflow-hidden x-animated x-fadeInUp"
                                        style="margin-top: 21px;">
                                        <div class="container container-xxl px-xxl-9 d-block">
                                            <div class="row">
                                                @php
                                                    $categories = json_decode($categories);
                                                @endphp
                                                @foreach ($categories as $category)
                                                    <div class="col">
                                                        <!-- Heading -->
                                                        <h4 class="dropdown-header text-secondary fs-18 mb-1 lh-1">
                                                            {{ $category->name }}
                                                        </h4>
                                                        <!-- List -->
                                                        @foreach ($category->sub_categories as $subcategory)
                                                            <div class="dropdown-item">
                                                                <a class="dropdown-link"
                                                                    href="/search?subcategory_id=[{{ $subcategory->id }}]">
                                                                    {{ $subcategory->name }}
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                                @php
                                                    $random_product = json_decode($random_product);
                                                @endphp
                                                @if ($random_product)
                                                    <div class="col h-100">
                                                        <a href="/product/{{ $random_product->id }}">
                                                            <div class="card border-0 mt-2">
                                                                <img src="{{ $random_product->photo ? $random_product->photo : '/images/placeholder.png' }}"
                                                                    alt="{{ $random_product->title }}"
                                                                    class="card-img" style="height: 250px;">
                                                                <div
                                                                    class="card-img-overlay d-flex flex-column pt-xxl-7 pb-3 px-xxl-6">
                                                                    <p class="text-danger letter-spacing-01 font-weight-600 mb-2 text-uppercase"
                                                                        dir="ltr">
                                                                        {{ $random_product->offer_type }}
                                                                    </p>
                                                                    <h3 class="fs-34 text-secondary">
                                                                        {{ $random_product->title }}
                                                                    </h3>
                                                                    <div class="mt-auto">
                                                                        <a href="/product/{{ $random_product->id }}"
                                                                            class="btn btn-white hover-white bg-hover-secondary border-hover-secondary">
                                                                            {{ $random_product->offer_price > 0 ? $random_product->offer_price : $random_product->price }}
                                                                            {{ __('dashboard.sar') }}

                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li aria-haspopup="true" aria-expanded="false"
                                    class="nav-item py-2 py-xl-7 sticky-py-xl-6 px-0 px-xl-5">
                                    <a class=" nav-link p-0" href="/page/about-us">
                                        {{ __('trans.About Us') }}
                                    </a>
                                </li>
                                <li aria-haspopup="true" aria-expanded="false"
                                    class="nav-item py-2 py-xl-7 sticky-py-xl-6 px-0 px-xl-5">
                                    <a class=" nav-link p-0" href="/contact-us">
                                        {{ __('dashboard.contact_us') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="icons-sticky">
                            <div class="d-flex align-items-center justify-content-end">
                                <ul class="languages full-menu">
                                    @if (app()->getLocale() != 'en')
                                        <li class="languages__item">
                                            <a class="languages__link {{ app()->getLocale() == 'en' ? 'active' : 'active' }}"
                                                rel="alternate" hreflang="{{ 'en' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                                EN
                                            </a>
                                        </li>
                                    @else
                                        <li class="languages__item">
                                            <a class="languages__link {{ app()->getLocale() == 'ar' ? 'active' : 'active' }}"
                                                rel="alternate" hreflang="{{ 'ar' }}"
                                                href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                                ع
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                <layout-labels :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}"
                                    :statistic_data='{{ json_encode($statistic_data) }}'>
                                </layout-labels>
                            </div>
                        </div>
                        <div class="d-flex align-items-center d-xl-none">
                            <button class="navbar-toggler border-0 px-0 canvas-toggle" type="button" aria-label="toggler"
                                data-canvas="true" data-canvas-options='{"width":"250px","container":".sidenav"}'>
                                <span class="fs-24 toggle-icon"></span>
                            </button>
                            <div class="mx-auto">
                                
                                   
                                    <a class="navbar-brand d-inline-block mr-0 py-5" href="/">
                                        <img src="/asset/images/logogogogogo.png" alt="Jak">
                                    </a>

                                
                            </div>
                            <a href="#search-popup" data-gtf-mfp="true"
                                data-mfp-options='{"type":"inline","focus": "#keyword","mainClass": "mfp-search-form mfp-move-from-top mfp-align-top"}'
                                class="nav-search d-flex align-items-center">
                                <svg class="icon icon-magnifying-glass-light fs-28">
                                    <use xlink:href="#icon-magnifying-glass-light"></use>
                                </svg>
                                <span
                                    class="d-none d-xl-inline-block ml-2 font-weight-500">{{ __('dashboard.search') }}</span></a>
                        </div>

                    </nav>
                </div>
            </div>
            <div class="mobile-navigation-bar">
                <div class="tab-nav-container mobile-navigationbar">
                    <div class="tab tabb">
                        <a href="/">
                            <svg class="icon icon-shopping-bag-open-light">
                                <use xlink:href="#icon-shopping-bag-open-light"></use>
                            </svg>
                            <p>{{ __('dashboard.home') }}</p>
                        </a>
                    </div>
                    @if (!auth('client')->user())
                        <div class="tab tabb">
                            <a class="nav-link pr-3 py-0" href="javascript:void(0)" data-toggle="modal"
                                data-target="#sign-in">
                                <svg class="icon icon-user-light">
                                    <use xlink:href="#icon-user-light"></use>
                                </svg>
                                <p>{{ __('dashboard.login') }}</p>
                            </a>
                        </div>
                    @endif

                    @if (auth('client')->user())
                        <div class="tab drop-up-btn">
                            <!-- <img src="imgs/profile.svg" alt="profile"/> -->
                            <svg class="icon icon-user-light">
                                <use xlink:href="#icon-user-light"></use>
                            </svg>
                            <p>{{ __('dashboard.profile') }}</p>

                            <ul class="nav-profile-menu mobile-navigation-dropup">
                                <li class="credit_list">
                                    <b>{{ __('dashboard.my_balance') }} : </b>
                                    <p class="my-credit">{{ $statistic_data['balance'] }} {{ __('dashboard.sar') }}
                                    </p>
                                    <!-- <ul class="languages full-menu">
                                        @if (app()->getLocale() != 'en')
<li class="languages__item">
                                                <a class="languages__link {{ app()->getLocale() == 'en' ? 'active' : 'active' }}"
                                                    rel="alternate" hreflang="{{ 'en' }}"
                                                    href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                                    EN
                                                </a>
                                            </li>
@else
<li class="languages__item">
                                                <a class="languages__link {{ app()->getLocale() == 'ar' ? 'active' : 'active' }}"
                                                    rel="alternate" hreflang="{{ 'ar' }}"
                                                    href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                                    ع
                                                </a>
                                            </li>
@endif
                                    </ul> -->
                                </li>
                                <li><a class="dropdown-item" href="/my-orders">{{ __('dashboard.my_orders') }}</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="/notifications">{{ __('dashboard.notifications') }}</a></li>
                                <li><a class="dropdown-item" href="/addresses">{{ __('dashboard.addresses') }}</a>
                                </li>
                                <li><a class="dropdown-item" href="/account">{{ __('dashboard.edit_profile') }}</a>
                                </li>
                                <li><a class="dropdown-item" href="/logout">{{ __('dashboard.logout') }}</a></li>
                            </ul>
                        </div>
                        <div class="tab tabb">
                            <a href="/wishlist">
                                <svg class="icon icon-star-light">
                                    <use xlink:href="#icon-star-light"></use>
                                </svg>
                                <p>{{ __('dashboard.wishlist') }}</p>
                            </a>
                        </div>
                        <div class="tab tabb">
                            <a href="/cart">
                                <svg class="icon icon-shopping-bag-open-light">
                                    <use xlink:href="#icon-shopping-bag-open-light"></use>
                                </svg>
                                <p>{{ __('dashboard.cart') }}</p>
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </header>
        @yield('content')
        <footer class="pt-10 pt-lg-14 pb-11 footer bg-gray">
            <div class="container container-xl">
                <div class="row">
                    <div class="col-lg col-md-6 col-12 mb-7 mb-lg-0">
                        <h3 class="fs-20 mb-3">{{ __('trans.Contact Information') }}</h3>
                        <p class="fs-14 lh-185 mb-0"><strong class="text-secondary">+996
                                {{ App\Models\Settings::find(17)->value }}</strong>
                            <br>
                            {{ App\Models\Settings::find(19)->value }}
                        </p>
                    </div>
                    {{-- <div class="col-lg col-md-4  col-12 mb-7 mb-lg-0">
                        <h3 class="fs-20 mb-3">{{ __('dashboard.Useful Links') }}</h3>
                        <ul class="list-unstyled mb-0">
                            @foreach (@$page_categories as $category)
                                <li class="pb-1"><a href="/search"
                                        class="text-body lh-175">{{ $category->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div> --}}
                    <div class="col-lg col-md-6 col-12 mb-7 mb-lg-0">
                        <h3 class="fs-20 mb-3">{{ __('dashboard.information') }}</h3>
                        <ul class="list-unstyled mb-0">
                            <li class="pb-1"><a href="/page/about-us"
                                    class="text-body lh-175">{{ __('trans.About Us') }}</a></li>
                            <li class="py-1"><a href="/contact-us"
                                    class="text-body lh-175">{{ __('dashboard.contact_us') }} </a></li>
                            <li class="py-1"><a href="/page/conditions"
                                    class="text-body lh-175">{{ __('trans.Terms & Conditions') }}</a></li>
                            <li class="pt-1"><a href="/page/privacy"
                                    class="text-body lh-175">{{ __('trans.Policy') }} </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-5 col-12 mb-9 mb-lg-0">
                        <iframe title="my-location"
                            src="https://www.google.com/maps?q=24.7349558,46.7806304&hl=es&z=15&amp;output=embed"
                            width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
                <div class="row mt-0 mt-lg-15 align-items-center">
                    <div
                        class="col-12 col-md-6 col-lg-4 d-flex align-items-center order-2 order-lg-1 mt-4 mt-md-7 mt-lg-0">
                        <p id="footer-title" class="mb-0">© Jak {{ date('Y') }}</p>
                        <ul class="list-inline fs-18 ml-3 mb-0">
                            @if ($twitter)
                                <li class="list-inline-item mr-4">
                                    <a href="{{ $twitter }}"><i class="fab fa-twitter"></i></a>
                                </li>
                            @endif
                            @if ($facebook)
                                <li class="list-inline-item mr-4">
                                    <a href="{{ $facebook }}"><i class="fab fa-facebook-f"></i></a>
                                </li>
                            @endif
                            @if ($instagram)
                                <li class="list-inline-item mr-4">
                                    <a href="{{ $instagram }}"><i class="fab fa-instagram"></i></a>
                                </li>
                            @endif
                            @if ($youtube)
                                <li class="list-inline-item mr-4">
                                    <a href="{{ $youtube }}"><i class="fab fa-youtube"></i></a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-12 col-lg-4 text-md-center order-1 order-lg-2">
                        <img src="/asset/images/logogogogogo.png" alt="Jak">
                    </div>
                    {{-- <div class="col-12 col-md-6 col-lg-4 text-md-right order-3 mt-4 mt-md-7 mt-lg-0">
                        <img src="/asset/images/icon-pay.png" alt="Pay">
                    </div> --}}
                </div>
            </div>
        </footer>
        <!-- Vendors scripts -->
        @include('website.icons')
        <div class="position-fixed pos-fixed-bottom-right p-6 z-index-10">
            <a href="#mainheader"
                class="gtf-back-to-top text-decoration-none bg-white text-primary hover-white bg-hover-primary shadow p-0 w-48px h-48px rounded-circle fs-20 d-flex align-items-center justify-content-center"
                title="Back To Top"><i class="fal fa-arrow-up"></i></a>
        </div>
        <side-cart :items="{{ $items }}"></side-cart>
        <div class="mfp-hide search-popup mfp-with-anim" id="search-popup">
            <form method="get" action="/search">
                <div class="input-group position-relative">
                    <input type="text" id="keyword" name="keyword" autocomplete="off"
                        class="srchinput form-control border-2  bg-transparent text-white border-white fs-24 form-control-lg"
                        placeholder="{{ __('dashboard.search') }}" style="width: 50px">
                    <div id="dropdown-menu"
                        class="dropdown-menu srch"style="top: -100px;direction: rtl;border-radius: 11px;padding: 20px 1px;"
                        aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#">{{ __('dashboard.search') }}</a>
                    </div>
                    <div class="input-group-append position-absolute pos-fixed-right-center">
                        <button class="input-group-text bg-transparent border-0 text-white fs-30 px-0 btn-lg" aria-label="search"
                            type="submit"><i class="far fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        @include('website.mobile-layout')
        <sign-in></sign-in>
        <register :countries="{{ $countries }}" :clienttypes="{{ $clientTypes }}"></register>
        <activation></activation>
        <view-product :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}"></view-product>
        <vue-confirm-dialog></vue-confirm-dialog>
        <cart-box></cart-box>
    </div>
    <!--js files-->
</body>
<script src="/js/app.js"></script>
<script src="/asset/vendors/jquery.min.js"></script>
{{-- <script src="/asset/vendors/jquery-ui/jquery-ui.min.js"></script> --}}
{{-- <script src="/asset/vendors/bootstrap/bootstrap.bundle.js"></script> --}}
{{-- <script src="/asset/vendors/bootstrap-select/js/bootstrap-select.min.js"></script> --}}
<script src="/asset/vendors/slick/slick.min.js"></script>
<script src="/asset/vendors/waypoints/jquery.waypoints.min.js"></script>
<script src="/asset/vendors/counter/countUp.js"></script>
<script src="/asset/vendors/magnific-popup/jquery.magnific-popup.min.js"></script>
<script src="/asset/vendors/hc-sticky/hc-sticky.min.js"></script>
{{-- <script src="/asset/vendors/jparallax/TweenMax.min.js"></script> --}}
{{-- <script src="/asset/vendors/mapbox-gl/mapbox-gl.js"></script> --}}
{{-- <script src="/asset/vendors/isotope/isotope.js"></script> --}}
<!-- Theme scripts -->
@if (app()->getLocale() == 'ar')
<script src="/asset/js/theme.js"></script>
@else
<script src="/asset/js/theme-en.js"></script>
@endif
<script>
    $(function() {
        "use strict";
        var e = $("body");
        e.on("keyup", ".verify-input", function(t) {
            var n = t.which,
                a = $(t.target).next("input");
            if ((n >= 48 && n <= 57) || (n >= 96 && n <= 105)) {
                return 9 === n || (a && a.length || (a = e.find("input.verify-input").eq(0)), void a
                    .select().focus())
            } else {
                return (t.preventDefault(), !1);
            }
            // return 9 != n && ((n < 48 || n > 57) )  ? (t.preventDefault(), !1) : 9 === n || (a && a.length || (a = e.find("input.verify-input").eq(0)), void a.select().focus())
        }), e.on("keydown", ".verify-input", function(e) {
            var t = e.which;
            console.log(t);
            return 9 === t || (t >= 48 && t <= 57) || (t >= 96 && t <= 105) || (e.preventDefault(), !1)
        }), e.on("click", ".verify-input", function(e) {
            $(e.target).select()
        })
    });
    $('#search_box').on('keyup', function() {
        var query = $(this).val();
        // if (query != '') {
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "{{ route('autocomplete.fetch') }}",
            method: "POST",
            data: {
                query: query,
                _token: _token
            },
            success: function(data) {
                $('#searchList').fadeIn();
                $('#searchList').html(data);
            }
        });
        // }
    });

    // $(window).on('load', function() {
    //     setTimeout(function() { // allowing 3 secs to fade out loader
    //         $('#spinner-container').fadeOut('slow');
    //     }, 1000);
    // });

    $(window).on('load', function() {
        setTimeout(function() { // allowing 3 secs to fade out loader
            $('#spinner-containerr').fadeOut('slow');
        }, 3500);
    });
    $(function() {
        $(".slider").not('.slick-initialized').slick();
    })
</script>
<script>
    @if (auth('client')->check())
        var getUser = {!! auth('client')->user() !!};
    @else
    @endif
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
    integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
</script>
<style>
    .mx-n2.slick-slider .slick-track {
        display: -ms-flexbox;
        display: flex;
        direction: rtl !important;
    }

    .search-box {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translate(0, -50%);
        height: 40px;
        background: #fff;
        line-height: 40px;
        padding: 5px;
        border: 1px solid #d183321F;
        border-radius: 2px;
        z-index: 999;
    }

    #search_box_result {
        /* z-index: 1000; */
        display: none;
        min-width: 10rem;
        padding: 0.5rem 0;
        margin: 0;
        font-size: 1rem;
        color: #212529;
        text-align: right;
        list-style: none;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid rgba(0, 0, 0, .15);
        border-radius: 0.25rem;
    }
</style>

</html>
