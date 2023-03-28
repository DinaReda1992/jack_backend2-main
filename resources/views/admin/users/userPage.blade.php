@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/visualization/d3/d3.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/ui/moment/moment.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/pickers/daterangepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>

    <script type="text/javascript" src="/assets/js/demo.js"></script>
    <script src="/assets/js/html-duration-picker.js" type="module"></script>
    <link rel="stylesheet" href="/assets/js/plugins/daterangepicker/daterangepicker-bs3.css">

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <style>
        .products-list {
            max-height: 244px;
            overflow-y: scroll;
            overflow-x: hidden;

        }

        .title-slug {
            font-size: 14px;
            margin-top: 5px;
            margin-bottom: 8px;
            color: gray;
        }
    </style>
    <script></script>
    <script type="text/javascript" src="/assets/js/admin/dashboardCharts.js"></script>

    <script type="text/javascript" src="/assets/js/pages/dashboard.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/dashboard2.js"></script>

@stop
@section('content')
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content row">


                <div class="page-title" style="margin-bottom: 86px;">
                    <div
                        style="margin-top: 20px;margin-{{ __('dashboard.right') }}: 50px;float: {{ __('dashboard.right') }};">
                        <div class="content-group">
                            <h5 class="text-semibold no-margin"><i
                                    class="fa fa-money position-{{ __('dashboard.right') }} text-slate"></i>
                                <span id="">{{ round($balance, 2) }} {{ __('dashboard.sar') }}</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-md-6" style="float: {{ __('dashboard.left') }};">
                        <img class="profile-user-img img-responsive img-circle"
                            style="float: {{ __('dashboard.left') }};margin-{{ __('dashboard.right') }}: 19px;"
                            src="{{ $object->photo }}" alt="User profile picture">
                        <h3 style="margin-top: 17px;">{{ $object->username }}</h3>
                        <ul class="icons-list">
                            <li class="text-danger-600">
                                <a class="btn btn-primary" style="color: #fff;"
                                    href="/admin-panel/all-users/{{ $object->id }}/edit"><i class="icon-pencil7"></i>
                                    {{ __('dashboard.edit') }}</a>
                            </li>
                            <li class="text-danger-600">
                                <a href="/admin-panel/all-users/block_user/{{ $object->id }}" class="btn btn-flickr"
                                    style="color: #fff;"><i class="icon-power-cord"></i> {{ __('dashboard.block') }}</a>
                            </li>
                            <li class="text-danger-600">

                                <a onclick="return false;" object_id="{{ $object->id }}"
                                    delete_url="/admin-panel/all-users/{{ $object->id }}"
                                    class="sweet_warning btn btn-danger" style="color: #fff;" href="#"><i
                                        class="icon-trash"></i> {{ __('dashboard.delete') }}</a>
                            </li>
                        </ul>

                    </div>

                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active">{{ __('dashboard.dashboard') }}</li>
                </ul>

            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            @include('admin.message')
            <!-- Main charts -->
            <div class="row">

                <div class="col-lg-12">
                    <!-- Sales stats -->
                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h6 class="panel-title">{{ __('dashboard.events') }} </h6>

                        </div>

                        <div class="container-fluid">

                            <div class="row text-center">
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('payment_method', '<>', 0)->count() }} </h3>
                                            <p>{{ __('dashboard.all_orders') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status="
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('payment_method', '<>', 0)->where('status', 0)->count() }}
                                            </h3>
                                            <p>{{ __('dashboard.new_orders') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=0"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('status', 1)->count() }} </h3>
                                            <p>{{ __('dashboard.accepted_orders') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=1"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>
                                                {{ $object->orders->where('status', 1)->where('marketed_date', '!=', null)->where('financial_date', '!=', null)->count() }}
                                            </h3>
                                            <p>{{ __('dashboard.orders_in_finance') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=2"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->



                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('status', 3)->count() }} </h3>
                                            <p>{{ __('dashboard.orders_in_warehouse') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=3"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('status', 5)->count() }} </h3>
                                            <p>{{ __('dashboard.cancelled_orders') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=5"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->
                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('status', 6)->count() }} </h3>
                                            <p>{{ __('dashboard.delivering_orders') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=6"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ $object->orders->where('status', 7)->count() }} </h3>
                                            <p>{{ __('dashboard.completed_orders') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-cart-plus"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/orders/{{ $object->id }}?status=7"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->

                                <div class="col-lg-3 col-xs-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua" style="background-color: #6c6c6c  !important;">
                                        <div class="inner">
                                            <h3>{{ round($balance, 2) }} {{ __('dashboard.sar') }}</h3>
                                            <p>{{ __('dashboard.transactions') }}</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fa fa-money"
                                                style="float: left;margin-top: 22px;font-size: 75px;"></i>
                                        </div>
                                        <a href="/admin-panel/all-users/transactions/{{ $object->id }}"
                                            class="small-box-footer">
                                            {{ __('dashboard.view_details') }}
                                            <i class="fa fa-arrow-circle-left"></i></a>
                                    </div>
                                </div><!-- ./col -->

                            </div>
                        </div>
                    </div>
                    <!-- /sales stats -->
                </div>
            </div>
            <!-- /main charts -->
            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->
        </div>
        <!-- /content area -->
    </div>
@stop
