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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">{{__('dashboard.dashboard')}}</span> -
                        {{__('dashboard.purchase_orders')}}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{__('dashboard.home')}} </a></li>
                    <li class="active"><a href="">{{__('dashboard.purchase_orders')}}</a></li>
                </ul>

            </div>
        </div>
        <!-- Content area -->
        <div class="content">
            @include('admin.message')
            <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{__('dashboard.purchase_orders')}}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>
                <div style=" text-align: center;">
                    <ul class="nav nav-tabs" role="tablist">
                        <li
                            class="{{ app('request')->input('status') && app('request')->input('status') == 'shipping' ? 'active' : '' }}">
                            <a href="/admin-panel/supplier-orders?status=shipping">{{__('dashboard.shipping')}}
                                ({{ $purchases_orders->shipping_orders }})
                            </a>
                        </li>
                        <li
                            class="{{ app('request')->input('status') && app('request')->input('status') == 'in_shipment' ? 'active' : '' }}">
                            <a href="/admin-panel/supplier-orders?status=in_shipment">{{__('dashboard.shipped')}}
                                ({{ $purchases_orders->in_shipment }})</a>
                        </li>
                        <li
                            class="{{ app('request')->input('status') && app('request')->input('status') == 'progress_shipment' ? 'active' : '' }}">
                            <a href="/admin-panel/supplier-orders?status=progress_shipment">{{__('dashboard.delivering')}}
                                ({{ $purchases_orders->progress_shipment }})</a>
                        </li>
                        <li
                            class="{{ app('request')->input('status') && app('request')->input('status') == 'completed' ? 'active' : '' }}">
                            <a href="/admin-panel/supplier-orders?status=completed">{{__('dashboard.delivered')}}
                                ({{ $purchases_orders->completed_orders }})</a>
                        </li>
                    </ul>
                </div>

                <div class="clearfix"></div>
                <br>
                <form method="get" action="/admin-panel/supplier-orders">
                    <div class="row">
                        <div align="center" class="col-md-12">
                            <div class="col-md-5" align="center">
                                <input name="order_id" type="text" class="form-control"
                                    value="{{ @request()->order_id }}" placeholder="{{__('dashboard.search_by_order_id')}}">
                            </div>
                            <div class="col-md-2" align="center">
                                <button class="btn btn-success">{{__('dashboard.search')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="clearfix"></div>
                <br>

                @include('admin.shipment.supplier_orders_details', ['type' => 0])

            </div>
            @include('admin.footer')
        </div>

    </div>
@stop
