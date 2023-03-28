@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/datatables.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/bootbox.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    @if (app()->getLocale() == 'ar')
        <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis.js"></script>
    @else
        <script type="text/javascript" src="/assets/js/pages/datatables_extension_colvis_en.js"></script>
    @endif
    <script type="text/javascript" src="/assets/js/pages/components_modals.js"></script>
    <link rel="stylesheet" href="/site/css/jquery.rateyo.min.css">
    <script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->


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
                        {{ __('dashboard.view_sales_report') }} </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"><a href="">{{ __('dashboard.view_sales_report') }} </a></li>
                </ul>
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">
            @include('admin.message')
            <!-- Basic example -->
            <form action="" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('dashboard.date_from') }}</label>
                            <input type="date" name="from" class="form-control datepicker"
                                value="{{ request('from') ?:now()->subYears(4)->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('dashboard.date_to') }}</label>
                            <input type="date" name="to" class="form-control datepicker"
                                value="{{ request('to') ?: now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('dashboard.employee') }}</label>
                            <select name="employee_id" class="form-control">
                                <option value="">{{ __('dashboard.all') }}</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}" @if (request('employee_id') == $employee->id) selected @endif>
                                        {{ $employee->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('dashboard.client') }}</label>
                            <select name="user_id" class="form-control select-multiple-tokenization">
                                <option value="">{{ __('dashboard.all') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if (request('user_id') == $user->id) selected @endif>
                                        {{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('dashboard.supplier') }}</label>
                            <select name="supplier_id" class="form-control select-multiple-tokenization">
                                <option value="">{{ __('dashboard.all') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @if (request('supplier_id') == $supplier->id) selected @endif>
                                        {{ $supplier->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('dashboard.region') }}</label>
                            <select name="region_id" class="form-control">
                                <option value="">{{ __('dashboard.all') }}</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region->id }}" @if (request('region_id') == $region->id) selected @endif>
                                        {{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>#</label><br>
                            <input type="submit" class="btn btn-success" value="{{ __('dashboard.search') }}">
                        </div>
                    </div>
                </div>
            </form>

            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.view_sales_report') }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li>
                                <a class="btn btn-default" href="#" onclick="downloadExcel()">
                                    {{ __('dashboard.export_excel') }} <i class="fa fa-file-excel"></i></a>
                            </li>
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table datatable-colvis-basic text-center  text-center">
                            <thead>
                                <tr>
                                    <th>{{ __('dashboard.orders_status') }}</th>
                                    <th>{{ __('dashboard.count_orders') }} </th>
                                    <th>{{ __('dashboard.sales') }} </th>
                                    <th>{{ __('dashboard.percentage_of_orders_from_status') }}</th>
                                    <th>{{ __('dashboard.percentage_of_orders_from_all') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>{{__('dashboard.new')}}</th>
                                    <td>{{ $data['new_orders'] }}</td>
                                    <td>{{ round($data['new_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_new_orders'] != 0 ? round(($data['new_orders'] / $data['all_new_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_new_orders'] != 0 ? round(($data['new_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>

                                </tr>
                                <tr>
                                    <th>{{__('dashboard.waiting_for_confirmation')}}</th>
                                    <td>{{ $data['pending_orders'] }}</td>
                                    <td>{{ round($data['pending_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_pending_orders'] != 0 ? round(($data['pending_orders'] / $data['all_pending_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_pending_orders'] != 0 ? round(($data['pending_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <th>{{__('dashboard.preparing')}}</th>
                                    <td>{{ $data['preparing_orders'] }}</td>
                                    <td>{{ round($data['preparing_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_preparing_orders'] != 0 ? round(($data['preparing_orders'] / $data['all_preparing_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_preparing_orders'] != 0 ? round(($data['preparing_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <th>{{__('dashboard.ready_to_ship')}}</th>
                                    <td>{{ $data['ready_to_ship_orders'] }}</td>
                                    <td>{{ round($data['ready_to_ship_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_ready_to_ship_orders'] != 0 ? round(($data['ready_to_ship_orders'] / $data['all_ready_to_ship_orders']) * 100, 2) : 0 }}
                                        %
                                    </td>
                                    <td>{{ $data['all_ready_to_ship_orders'] != 0 ? round(($data['ready_to_ship_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <th>{{__('dashboard.shipped')}}</th>
                                    <td>{{ $data['shipped_orders'] }}</td>
                                    <td>{{ round($data['shipped_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_shipped_orders'] != 0 ? round(($data['shipped_orders'] / $data['all_shipped_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_shipped_orders'] != 0 ? round(($data['shipped_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <th>{{ __('dashboard.delivering') }}</th>
                                    <td>{{ $data['delivering_orders'] }}</td>
                                    <td>{{ round($data['delivering_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_delivering_orders'] != 0 ? round(($data['delivering_orders'] / $data['all_delivering_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_delivering_orders'] != 0 ? round(($data['delivering_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <th>{{ __('dashboard.completed') }}</th>
                                    <td>{{ $data['completed_orders'] }}</td>
                                    <td>{{ round($data['completed_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_completed_orders'] ? round(($data['completed_orders'] / $data['all_completed_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_completed_orders'] ? round(($data['completed_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                                <tr>
                                    <th>{{__('dashboard.cancelled')}}</th>
                                    <td>{{ $data['canceled_orders'] }}</td>
                                    <td>{{ round($data['canceled_orders_price'], 2) }} {{ __('dashboard.sar') }}</td>
                                    <td>{{ $data['all_canceled_orders'] ? round(($data['canceled_orders'] / $data['all_canceled_orders']) * 100, 2) : 0 }}
                                        %</td>
                                    <td>{{ $data['all_canceled_orders'] ? round(($data['canceled_orders'] / $data['all_orders']) * 100, 2) : 0 }}
                                        %</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

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
    <script src="/site/js/jquery.rateyo.min.js"></script>

    <script type="text/javascript">
        const user_id = '{{ request('user_id') }}';
        const region_id = '{{ request('region_id') }}';
        const from = '{{ request('from') }}';
        const to = '{{ request('to') }}';
        const employee_id = '{{ request('employee_id') }}';

        function downloadExcel() {
            const url = '/admin-panel/download-sales-excel?region_id=' + region_id + '&from=' + from + '&to=' +
                '&employee_id=' + employee_id + '&user_id=' + user_id;
            const a = document.createElement("a");
            a.href = url;
            document.body.appendChild(a);
            a.click();
            a.remove();
        }
    </script>
@stop
