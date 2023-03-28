<!DOCTYPE html>
<html lang="{{ __('dashboard.lang') }}" dir="{{ __('dashboard.dir') }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>لوحة التحكم - {{ \App\Models\Settings::find(2)->value }}</title>
    <link rel="shortcut icon" href="/site/assets/img/favicon.png" type="image/x-icon">
    @yield('js_vue')

    <!-- Global stylesheets -->
    <!--<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <link href="/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    @if (app()->getLocale() == 'ar')
        <link href="/assets/css/AdminLTE-RTL.min.css" rel="stylesheet" type="text/css">
        {{-- <link rel="stylesheet" href="{{ asset('css/dashboard_ar.css') }}"> --}}
    @else
        <link href="/assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css">
        {{-- <link rel="stylesheet" href="{{ asset('css/dashboard_en.css') }}"> --}}
    @endif
    <style>
        .blockMsg {
            width: auto !important;
            left: 50% !important;
        }
    </style>

    <link href="/assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/components.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->
    <!-- Core JS files -->
    <script type="text/javascript" src="/assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/loaders/blockui.min.js"></script>
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <!-- /core JS files -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!--js code-->
    <script type="text/javascript">
        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                $(".side_list").filter(function() {
                    $(this).find('.hidden-ul').css('display', "block");
                    $(this).toggle($(this).find('h5,span').text().toLowerCase().trim().indexOf(
                        value) > -1)
                    if ($("#myInput").val().toLowerCase() == '') {
                        $('.hidden-ul').css('display', "none");
                    }
                });
            });
        })
    </script>
</head>

<body>
    <!-- Main navbar -->
    <div class="navbar navbar-inverse" style="background: {{ \App\Models\Settings::find(33)->value }}">
        <div class="navbar-header" style="float: {{ __('dashboard.left') }};margin-{{ __('dashboard.left') }}:-20px;">
            <a class="navbar-brand" style="float: {{ __('dashboard.left') }};" target="_blank"
                href="#"><span>{{ __('dashboard.dashboard') }}
                    {{ \App\Models\Settings::find(2)->value }}</span></a>

            <ul class="nav navbar-nav visible-xs-block">
                <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
                <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
            </ul>
        </div>

        <div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav"
                style="float: {{ __('dashboard.left') }};margin-{{ __('dashboard.left') }}:20px;">
                <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a>
                </li>
            </ul>

            <p class="navbar-text" style="float: {{ __('dashboard.left') }};padding-{{ __('dashboard.right') }}: 0;">
                <span class="label bg-success-400">{{ __('dashboard.online') }}</span>
            </p>


            <ul class="nav navbar-nav navbar-{{ __('dashboard.left') }}" style="display: flex;">

                @php
                    $contacts = \App\Models\Contacts::where('status', 0)->count();
                    $suggestions = \App\Models\Suggestions::where('status', 0)->count();
                    $providers = \App\Models\RequestProvider::where('status', 0)->count();
                    $approved_orders = \App\Models\Notification::where('reciever_id', Auth::user()->id)
                        ->where('status', 1)
                        ->count();
                    $payed_orders = \App\Models\Notification::where('reciever_id', Auth::user()->id)
                        ->where('status', 2)
                        ->count();
                    $on_progress_orders = \App\Models\Notification::where('reciever_id', Auth::user()->id)
                        ->where('status', 3)
                        ->count();
                    $done_orders = \App\Models\Notification::where('reciever_id', Auth::user()->id)
                        ->where('status', 4)
                        ->count();
                    $cancelled_orders = \App\Models\Notification::where('reciever_id', Auth::user()->id)
                        ->where('status', 5)
                        ->count();
                    $bank_transfers = \App\Models\BankTransfer::where('status', 0)->count();
                    $tickets = \App\Models\Messages::where('status', 0)
                        ->where('reciever_id', 1)
                        ->count();
                    $cont_all = $contacts + $tickets;
                @endphp
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        @if (app()->getLocale() != 'ar')
                            English
                        @else
                            العربية
                        @endif
                        <i class="las la-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu" style="width: 50px">
                        @if (app()->getLocale() == 'ar')
                            <li>
                                <a class="dropdown-item" rel="alternate" hreflang="{{ 'en' }}"
                                    href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                    English
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="dropdown-item" rel="alternate" hreflang="{{ 'ar' }}"
                                    href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                    العربية
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="icon-bell2"></i>
                        <span class="visible-xs-inline-block position-right">{{ __('dashboard.notifications') }}</span>
                        @if ($cont_all > 0)
                            <span class="badge bg-warning-400">{{ $cont_all }}</span>
                        @endif
                    </a>

                    <div class="dropdown-menu dropdown-content width-350">
                        <div class="dropdown-content-heading">
                            {{ __('dashboard.notifications') }}
                        </div>
                        <ul class="media-list dropdown-content-body">

                            <li class="media" dir="rtl">
                                <div class="media-{{ __('dashboard.right') }}">
                                    <img src="/assets/images/placeholder.jpg" class="img-circle img-sm"
                                        alt="">
                                    <span class="badge bg-danger-400 media-badge">{{ $tickets }}</span>
                                </div>

                                <div class="media-body">
                                    <a href="/admin-panel/display/new_messages" class="media-heading">
                                        <span class="text-semibold"> {{ __('dashboard.new_ticket') }} </span>
                                    </a>

                                    <span
                                        class="text-muted">{{ __('dashboard.you_have_new_tickets', ['ticket' => $tickets]) }}</span>
                                </div>
                            </li>
                            <li class="media" dir="rtl">
                                <div class="media-{{ __('dashboard.right') }}">
                                    <img src="/assets/images/placeholder.jpg" class="img-circle img-sm"
                                        alt="">
                                    <span class="badge bg-danger-400 media-badge">{{ $contacts }}</span>
                                </div>

                                <div class="media-body">
                                    <a href="/admin-panel/display/contacts_new" class="media-heading">
                                        <span class="text-semibold"> {{ __('dashboard.communication_messages') }}
                                        </span>
                                    </a>

                                    <span
                                        class="text-muted">{{ __('dashboard.you_have_new_messages', ['message' => $contacts]) }}</span>
                                </div>
                            </li>
                        </ul>
                        <div class="dropdown-content-footer">
                            <a href="#" data-popup="tooltip" title="All messages"><i
                                    class="icon-menu display-block"></i></a>
                        </div>
                    </div>
                </li>

                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <img src="/uploads/{{ Auth::user()->photo }}" alt="{{ Auth::user()->username }}">
                        <span>{{ Auth::user()->username }}</span>
                        <i class="caret"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-{{ __('dashboard.left') }}">
                        <li><a href="/admin-panel/edit-profile"><i class="icon-user-plus"></i>
                                {{ __('dashboard.profile') }}</a></li>
                        <li><a href="/admin-panel/logout"><i class="icon-switch2"></i>
                                {{ __('dashboard.logout') }}</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <!-- /main navbar -->

    <!-- Page container -->
    <div class="page-container" id="app">

        <!-- Page content -->
        <div class="page-content">

            <!-- Main sidebar -->
            <div class="sidebar sidebar-main" style="background: {{ \App\Models\Settings::find(33)->value }}">
                <div class="ovauto sidebar-content">

                    <!-- User menu -->
                    <div class="sidebar-user">
                        <div class="category-content">
                            <div class="media">
                                <a href="/admin-panel/edit-profile" class="media-{{ __('dashboard.right') }}"><img
                                        src="/uploads/{{ Auth::user()->photo }}" class="img-circle img-sm"
                                        alt="{{ Auth::user()->username }}"></a>
                                <div class="media-body">
                                    <span class="media-heading text-semibold">{{ Auth::user()->username }}</span>
                                    <div class="text-size-mini text-muted">
                                        <i class="icon-pin text-size-small"></i> {{ __('dashboard.saudi_arabia') }}
                                    </div>
                                </div>

                                <div class="media-right media-middle">
                                    <ul class="icons-list side_list">
                                        <li class="side_list">
                                            <a href="/admin-panel/edit-profile"><i class="icon-cog3"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main navigation -->
                    <div class="sidebar-category sidebar-category-visible">
                        <div class="category-content no-padding">
                            <div>
                                <form action="#" method="get" class="sidebar-form">
                                    <div class="input-group" style="margin: auto; width:85%;margin-bottom: 15px;">
                                        <input type="text" id="myInput" style="height: 30px; padding:15px"
                                            name="q" class="form-control"
                                            placeholder="{{ __('dashboard.search') }}" autocomplete="off">
                                    </div>
                                </form>
                            </div>
                            <ul class="navigation navigation-main navigation-accordion side_list">

                                <!-- Main -->
                                <li class="@if (Request::is(app()->getlocale() . '/admin-panel')) active @endif"><a
                                        href="/{{ app()->getlocale() }}/admin-panel"><i class="icon-home4"
                                            style="font-size: 15px;float: {{ __('dashboard.left') }}; margin-{{ __('dashboard.right') }}: 15px; margin-{{ __('dashboard.left') }}: 0px;"></i>
                                        <span>{{ __('dashboard.home') }}</span></a></li>
                                @if (Auth::User()->user_type_id == 1)
                                    <?php $privileges = \App\Models\Privileges::where('parent_id', 0)
                                        ->where('hidden', 0)
                                        ->where('is_provider', 0)
                                        ->orderBy('orders', 'ASC')
                                        ->get(); ?>
                                @elseif(Auth::User()->user_type_id == 2 && !empty(Auth::user()->privilege_id))
                                    <?php
                                    $pr = \App\Models\PrivilegesGroupsDetails::where('privilege_group_id', Auth::user()->privilege_id)
                                        ->pluck('privilege_id')
                                        ->toArray();
                                    $privileges = \App\Models\Privileges::whereIn('id', $pr)
                                        ->where('parent_id', 0)
                                        ->where('is_provider', 0)
                                        ->orderBy('orders', 'ASC')
                                        ->get();
                                    
                                    ?>
                                @else
                                    <?php
                                    $privileges = \App\Models\Privileges::where('parent_id', 0)
                                        ->where('is_provider', 0)
                                        ->get();
                                    ?>
                                @endif
                                @foreach ($privileges as $privilege)
                                    @php $active="" @endphp
                                    @if ($privilege->subProgrames->count())
                                        @foreach ($privilege->subProgrames->where('hidden', 0) as $program)
                                            @if (Request::is(ltrim(app()->getLocale() . $program->url, '/')) || Request::is(ltrim($program->url, '/')))
                                                @php  $active="active" @endphp
                                            @endif
                                        @endforeach
                                        <li class="side_list {{ $active }}">
                                            <a href="#" style="padding-{{ __('dashboard.left') }}: 20px;"><i
                                                    style="font-size: 15px;float: {{ __('dashboard.left') }}; margin-{{ __('dashboard.right') }}: 15px; margin-{{ __('dashboard.left') }}: 0px;"
                                                    class="{{ $privilege->icon }}"></i>
                                                <span>{{ __($privilege->privilge) }}</span></a>
                                            <ul>
                                                @foreach ($privilege->subProgrames->where('hidden', 0) as $program)
                                                    <li class="side_list"><a href="{{ $program->url }}">
                                                            <span>{{ __($program->privilge) }}</span></a></li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        @if (isset($privilege->url) && Request::is(ltrim(app()->getlocale() . $privilege->url)))
                                            @php  $active="active" @endphp
                                        @endif

                                        <li class="side_list {{ $active }}"><a href="{{ $privilege->url }}"><i
                                                    style="font-size: 15px;float: {{ __('dashboard.left') }}; margin-{{ __('dashboard.right') }}: 15px; margin-{{ __('dashboard.left') }}: 0px;"
                                                    class="{{ $privilege->icon }}"></i>
                                                <span>{{ __($privilege->privilge) }}</span></a></li>
                                    @endif
                                @endforeach
                                @if (auth()->user()->id == 13)
                                    <li class="side_list @if (Request::is('admin-panel/privileges') or Request::is('admin-panel/privileges/*')) active @endif">
                                        <a href="#"><i style="font-size: 15px;"
                                                class="glyphicon glyphicon-gift"></i> <span>القائمة
                                                الجانبية</span></a>
                                        <ul>
                                            <li class="side_list"><a href="/admin-panel/privileges/create">اضافة
                                                    قائمة</a></li>
                                            <li class="side_list"><a href="/admin-panel/privileges">عرض القوائم</a>
                                            </li>
                                            <li class="side_list"><a href="/admin-panel/privileges-hidden">عرض
                                                    المخفية</a></li>
                                        </ul>
                                    </li>
                                @endif


                            </ul>
                        </div>
                    </div>
                    <!-- /main navigation -->



                    <!-- /user menu -->
                    <!-- Main navigation -->
                    {{-- <div class="sidebar-category sidebar-category-visible"> --}}
                    {{-- <div class="category-content no-padding"> --}}
                    {{-- <ul class="side_list navigation navigation-main navigation-accordion"> --}}
                    {{-- <!-- Main --> --}}
                    {{-- <li class="side_list navigation-header"><span>القائمة</span> <i class="icon-menu" title="Main pages"></i></li> --}}
                    {{-- <li class="side_list @if (Request::is('admin-panel/index')) active @endif"><a href="/admin-panel/index"><i class="icon-home4"></i> <span>{{ __('dashboard.home') }}</span></a></li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/settings')) active @endif"><a href="/admin-panel/settings"><i class="icon-gear"></i> <span>اعدادات التطبيق</span></a></li> --}}
                    {{-- <li class="side_list @if (Request::is('admin-panel/menus/*') or Request::is('admin-panel/menus')) active @endif"> --}}
                    {{-- <a href="#"><i class="fa fa-edit"></i> <span>القوائم</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/menus">عرض القوائم</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/menus/create">اضافة قائمة</a></li> --}}

                    {{-- </ul> --}}
                    {{-- </li> --}}
                    {{-- <li  class="side_list @if (Request::is('admin-panel/main_slider/*') or Request::is('admin-panel/main_slider')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-stack"></i> <span>السلايدر</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/main_slider">عرض الشرائح</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/main_slider/create">اضافة سلايدر</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/send_notifications') or Request::is('admin-panel/send_notifications/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-bell3"></i> <span>اشعارات التطبيق</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/send_notifications/create">ارسال اشعارات جماعية</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/send_notifications">عرض {{ __('dashboard.notifications') }} الجماعية</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}
                    {{-- <li class="side_list @if (Request::is('admin-panel/illustrations') or Request::is('admin-panel/illustrations/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-screenshot"></i> <span>الشاشات الافتتاحية</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/illustrations/create">اضافة شاشة افتتاحية</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/illustrations">عرض الشاشات الافتتاحية</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}
                    {{-- <li class="side_list @if (Request::is('admin-panel/users') or Request::is('admin-panel/users/*') or Request::is('admin-panel/adv_users') or Request::is('admin-panel/normal_users')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-users"></i> <span>الاعضاء</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/users/create">أضف عضو</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/users">عرض جميع الاعضاء</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/normal_users">عرض العضويات العادية</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/adv_users">عرض العضويات الذهبية	</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}


                    {{-- <li class="side_list @if (Request::is('admin-panel/orders') or Request::is('admin-panel/users/*') or Request::is('admin-panel/adv_users') or Request::is('admin-panel/normal_users')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-shield-notice"></i> <span>طلبات الفحص</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/new_orders">الطلبات الجديدة ( {{ \App\Models\Orders::where('status',0)->count() }} )</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/approved_orders">طلبات تم قبولها ( {{  \App\Models\Orders::where('status',1)->count() }} )</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/payed_orders">طلبات تم دفع تكلفتها ( {{  \App\Models\Orders::where('status',2 )->count()}} )</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/on_progress_orders">طلبات جاري العمل عليها ( {{  \App\Models\Orders::where('status',3)->count() }} )</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/done_orders">طلبات مكتملة ( {{  \App\Models\Orders::where('status',4)->count() }} )</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/cancelled_orders">طلبات ملغاه ( {{  \App\Models\Orders::where('status',5)->count() }} )</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/packages') or Request::is('admin-panel/packages/*') or Request::is('admin-panel/membership_benefits') or Request::is('admin-panel/membership_benefits/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-stack"></i> <span>باقات الاشتراك</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/packages/create">اضافة باقة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/packages">عرض الباقات</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/membership_benefits/create">اضافة ميزة للباقة الذهبية</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/membership_benefits">عرض مميزات الباقة الذهبية</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/bank-transfer-order') or Request::is('admin-panel/bank-transfer-member') or Request::is('admin-panel/membership_benefits') or Request::is('admin-panel/membership_benefits/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-coin-dollar"></i> <span>طلبات الدفع</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/packages/create">اضافة باقة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/bank-transfer-order">طلبات الدفع للخدمات</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/bank-transfer-member">طلبات الدفع للعضويات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}


                    {{-- <li class="side_list @if (Request::is('admin-panel/services') or Request::is('admin-panel/services/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-tasks"></i> <span>الخدمات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/services/create">اضافة خدمة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/services">عرض الخدمات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}


                    {{-- <li class="side_list @if (Request::is('admin-panel/report-types/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-bookmark"></i> <span>أقسام نماذج التقارير</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/report-types/create">اضافة قسم نموذج</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/report-types">عرض أقسام النماذج</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}


                    {{-- <li class="side_list @if (Request::is('admin-panel/reports-details/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-bookmark"></i> <span>نماذج التقارير</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- @foreach (\App\Models\Services::all() as $service) --}}
                    {{-- <li class="side_list"><a href="/admin-panel/reports-details/{{ $service->id }}"> نموذج {{ $service->name }}  </a></li> --}}
                    {{-- @endforeach --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/countries') or Request::is('admin-panel/countries/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-map-marker"></i> <span>الدول</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/countries/create">اضافة دولة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/countries">عرض الدول</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}
                    {{-- <li class="side_list @if (Request::is('admin-panel/states') or Request::is('admin-panel/states/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-map-marker"></i> <span>المناطق</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/states/create">اضافة منطقة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/states">عرض المناطق</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/cars') or Request::is('admin-panel/cars/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="fa fa-car"></i> <span>ماركات السيارات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/cars/create">اضافة ماركة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/cars">عرض الماركات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/carsmodels') or Request::is('admin-panel/carsmodels/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="fa fa-car"></i> <span>موديلات السيارات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/carsmodels/create">اضافة موديل</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/carsmodels">عرض الموديلات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/years') or Request::is('admin-panel/years/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="fa fa-calendar"></i> <span>سنوات الصنع</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/years/create">اضافة سنة صنع</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/years">عرض سنوات الصنع</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}


                    {{-- <li class="side_list @if (Request::is('admin-panel/categories') or Request::is('admin-panel/categories/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-stack"></i> <span>الأقسام الرئيسة</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/categories/create">اضافة قسم رئيسي</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/categories">عرض الاقسام</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}
                    {{-- <li class="side_list @if (Request::is('admin-panel/subcategories') or Request::is('admin-panel/subcategories/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-stack"></i> <span>الأقسام الفرعية</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/subcategories/create">اضافة قسم فرعي</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/subcategories">عرض الاقسام</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}


                    {{-- <li class="side_list @if (Request::is('admin-panel/products') || Request::is('admin-panel/products/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-cart5"></i> <span>المنتجات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/products/create">اضافة منتج</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/products">عرض المنتجات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/content')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-stack"></i> <span>الصفحات الثابتة</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- @foreach (\App\Models\Content::where('type', 1)->get() as $content) --}}
                    {{-- <li class="side_list"><a href="/admin-panel/content/{{ $content -> id }}/edit">{{ $content -> page_name }}</a></li> --}}
                    {{-- @endforeach --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}




                    {{-- <li class="side_list @if (Request::is('admin-panel/content/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-zoom-in"></i> <span>الكلمات المفتاحية</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- @foreach (\App\Models\Content::all() as $content) --}}
                    {{-- <li class="side_list"><a href="/admin-panel/content/{{ $content -> id }}/edit">{{ $content -> page_name }}</a></li> --}}
                    {{-- @endforeach --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/faqs') or Request::is('admin-panel/faqs/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-question-sign"></i> <span>الاسئلة المكررة</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/faqs/create">اضافة سؤال</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/faqs">عرض الاسئلة</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/currencies') or Request::is('admin-panel/currencies/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-coin-dollar"></i> <span>العملات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/currencies/create">اضافة عملة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/currencies">عرض العملات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/join_us') or Request::is('admin-panel/join_us/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-users2"></i> <span>طلبات الالتحاق</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/display/new_join_us">( {{ \App\Models\JoinUs::where('status',0)->count() }} ) عرض الطلبات الجديدة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/join_us">كل الطلبات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/messages') or Request::is('admin-panel/display/new_messages')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-envelop"></i> <span>رسائل المحادثات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/display/new_messages"> عرض الرسائل الجديدة ( {{ \App\Models\Messages::where('status',0)->where('reciever_id',0)->count() }} )</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/messages">كل الرسائل</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/contacts') or Request::is('admin-panel/display/contacts_new')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-question4"></i> <span>الشكاوي والمقترحات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/display/contacts_new">( {{ \App\Models\Contacts::where('status',0)->count() }} ) عرض الشكاوي والمقترحات</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/contacts">كل الشكاوي والمقترحات</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/bank_accounts') or Request::is('admin-panel/bank_accounts/create')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-coin-dollar"></i> <span>الحسابات البنكية</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/bank_accounts/create">أضف حساب بنكي</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/bank_accounts">الحسابات البنكية</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/steps') or Request::is('admin-panel/steps/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-step-forward"></i> <span>خطوات الطلب</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/steps/create">اضافة خطوة</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/steps">خطوات الطلب</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/partners') or Request::is('admin-panel/partners/*')) active @endif"> --}}
                    {{-- <a href="#"><i class="glyphicon glyphicon-gift"></i> <span>شركاء أطياف</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/partners/create">اضافة شريك</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/partners">عرض الشركاء</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}

                    {{-- <li class="side_list @if (Request::is('admin-panel/pay_account') or Request::is('admin-panel/display/pay_account_new')) active @endif"> --}}
                    {{-- <a href="#"><i class="icon-cart5"></i> <span>طلبات الدفع للاشتراكات</span></a> --}}
                    {{-- <ul class="side_list"> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/pay_account">كل الطلبات</a></li> --}}
                    {{-- <li class="side_list"><a href="/admin-panel/display/pay_account_new">طلبات العضويات الجديدة ( {{ \App\Models\PayAccount::where('status',0)->count() }} )</a></li> --}}
                    {{-- </ul> --}}
                    {{-- </li> --}}
                    {{-- </ul> --}}
                    {{-- </div> --}}
                    {{-- </div> --}}
                    <!-- /main navigation -->

                </div>
            </div>
            <!-- /main sidebar -->
            <!-- Main content -->
            @yield('content')
            <!-- /main content -->

        </div>
        <!-- /page content -->
        @if (auth()->id() == 13)
            <div>
                <a href="/admin-panel/logs" target="_blank"><i
                        style=" position: fixed;
		bottom: 2px;
		left: 27px;
		font-size: 57px;"
                        class="fa fa-history"></i></a>
            </div>
        @endif
    </div>
    <!-- /page container -->
    <div id="notify_container"></div>
    @yield('js_files')
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script src="{{ url('/js/firebase.js') }}"></script>
    <script>
        $(".navigation > li > a").click(function() {

            $(this).toggleClass('active');

        });

        // sidebar width

        $(".sidebar-main-toggle").click(function() {

            $('.content-wrapper').toggleClass('activewidth');

        });
        // sidebar overflow

        $(".sidebar-main-toggle").click(function() {

            $('.sidebar-content').toggleClass('ovauto');
            $('.content-wrapper.only').toggleClass('add-product-content');

        });
        // sidebar height

        $(".sidebar-main-toggle").click(function() {

            $('.sidebar-main.sidebar').toggleClass('sidebarheight');
            $('.sidebar-main.sidebar').toggleClass('small-sidebar');

        });
        // page loader
        $(window).on('load', function() {
            setTimeout(function() { // allowing 3 secs to fade out loader
                $('.page-loader').fadeOut('slow');
            }, 1000);
        });
        $(window).on('load', function() {
            setTimeout(function() { // allowing 3 secs to fade out loader
                $('#spinner-containerr').fadeOut('slow');
            }, 1000);
        });
        // $(window).load(function() {
        //   $(".page-loader").fadeOut(1500);
        // });
    </script>
</body>

</html>
