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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -
                        عرض جميع طلبات الناقصة من السائق</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li class="active"><a href="">عرض جميع طلبات الناقصة من السائق</a></li>
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
                    <h5 class="panel-title">عرض جميع طلبات الناقصة من السائق</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="clearfix"></div>
                <br>
                <form method="get" action="">
                    <div class="row">
                        <div align="center" class="col-md-12">
                            <div class="col-md-3" align="center">
								<label>بحث برقم الطلب</label>
                                <input name="order_id" type="text" class="form-control"
                                    value="{{ @request()->order_id }}" placeholder="بحث برقم الطلب">
                            </div>
                            <div class="col-md-3" align="center">
								<label>بحث بسائق</label>
                                <select name="driver_id" class="form-control">
                                    <option value="">بحث بسائق</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}"
                                            {{ @request()->driver_id == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>التاريخ من</label>
                                    <input type="date" name="from" class="form-control datepicker"
                                        value="{{ request('from') ?:now()->subYears(4)->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>التاريخ إلى</label>
                                    <input type="date" name="to" class="form-control datepicker"
                                        value="{{ request('to') ?: now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-2" align="center">
								<label>#</label><br>
                                <button class="btn btn-success">بحث</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="clearfix"></div>
                <br>


                @include('admin.purchases.missing_orders_details', ['type' => 0])

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
        var modelId = "{{ old('id') }}"
        var modelName = '#myModal' + '{{ old('id') }}' + '555'
        if (modelId != "") {
            $(modelName).modal();
        }
    </script>

@stop
