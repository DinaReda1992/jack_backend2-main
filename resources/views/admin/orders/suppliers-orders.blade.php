@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
    <script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>

    <!-- /theme JS files -->

@stop
@section('content')


    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }}</span> -
                        عرض طلبات الموردين لعملائهم</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li class="active"><a href="">عرض طلبات الموردين لعملائهم</a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
            @include('admin.message')
            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{__('dashboard.view_all_orders')}}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                    @if (\Request::segment(2) == 'warehouse')
                        <div style=" text-align: center;">
                            <ul class="nav nav-tabs" role="tablist">

                                <li class="{{ !request()->status ? 'active' : '' }}">
                                    <a href="/admin-panel/warehouse">الكل</a>
                                </li>
                                <li class="{{ request()->status == 2 ? 'active' : '' }}">
                                    <a href="/admin-panel/suppliers-orders?status=2">قيد التجهيز</a>
                                </li>
                                <li class="{{ request()->status == 3 ? 'active' : '' }}">
                                    <a href="/admin-panel/suppliers-orders?status=3">{{__('dashboard.shipping')}}</a>
                                </li>
                                <li class="{{ request()->status == 4 ? 'active' : '' }}">
                                    <a href="/admin-panel/suppliers-orders?status=4">{{__('dashboard.shipped')}}</a>
                                </li>
                                <li class="{{ request()->status == 6 ? 'active' : '' }}">
                                    <a href="/admin-panel/suppliers-orders?status=6">{{__('dashboard.delivering')}}</a>
                                </li>
                                <li class="{{ request()->status == 7 ? 'active' : '' }}">
                                    <a href="/admin-panel/suppliers-orders?status=7">{{__('dashboard.completed')}}</a>
                                </li>

                            </ul>
                        </div>
                    @endif

                </div>

                <div class="clearfix"></div>
                <br>
                <form method="get" action="/admin-panel/orders/suppliers-orders">
                    <div class="row">
                        <div align="center" class="col-md-12">
                            <div class="col-md-5" align="center">
                                <input name="order_id" type="text" class="form-control" value="{{ request()->order_id }}"
                                    placeholder="{{__('dashboard.search_by_order_id')}}">
                            </div>
                            <div class="col-md-2" align="center">
                                <button class="btn btn-success">{{__('dashboard.search')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="clearfix"></div>
                <br>



                @include('admin.orders.orders_details')

            </div>
            <!-- /basic example -->


            <!-- Restore column visibility -->


            <!-- State saving -->

            <!-- Column groups -->


            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
    <script type="text/javascript">
        // Warning alert
    </script>

@stop
