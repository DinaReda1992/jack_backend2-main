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
                        {{ __('dashboard.view_missing_products') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li class="active"><a href="">{{ __('dashboard.view_missing_products') }}</a></li>
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
                    <h5 class="panel-title">{{ __('dashboard.view_missing_products') }}</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                    <div style=" text-align: center;">

                    </div>

                </div>
                <div class="clearfix"></div>
                <br>

                <div class="table-responsive text-center">
                    <table class="table no-margin text-center">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.product_id') }}</th>
                                <th>{{ __('dashboard.product_name') }}</th>
                                <th>{{ __('dashboard.quantity_in_warehouse') }}</th>
                                <th>{{ __('dashboard.min_quantity_in_warehouse') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouse_products as $product)
                                <tr>
                                    <td><a href="/admin-panel/products/{{ $product->id }}/edit"
                                            target="_blank">{{ $product->id }}</a></td>
                                    <td>{{ @$product->title }}</td>
                                    <td>{{ @$product->quantity }}</td>
                                    <td>{{ @$product->min_warehouse_quantity }}</td>
                                </tr>
                            @empty
                                <tr class="text-center">
                                    <td colspan="4" class="text-center">{{__('dashboard.not_found_product')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div>
            <!-- Footer -->
            @include('admin.footer')

        </div>
        <!-- /content area -->

    </div>
@stop
