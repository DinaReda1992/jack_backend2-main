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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script type="text/javascript" src="/assets/js/demo.js"></script>
    <script src="/assets/js/html-duration-picker.js" type="module" ></script>
    <link rel="stylesheet" href="/assets/js/plugins/daterangepicker/daterangepicker-bs3.css">

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <style>
        .products-list {
            max-height: 244px;
            overflow-y: scroll;
            overflow-x: hidden;

        }
    </style>
    <script>
        $(function() {
            window.onload = function() {
                $.ajax({
                    url: '/admin-panel/sms-balance',
                    success: function(data) {
                        if (data.status == true) {
                            $('#sms_balance').html(data.response);
                            $('#balance_note').html(data.message);

                        }

                    }
                });
            };
            var start = moment().subtract(29, 'days');
            var end = moment();


            function cb(start, end) {
                $('#reportrange span').html(start.format('D MMMM, YYYY') + ' - ' + end.format('D MMMM, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,

                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);
            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                console.log(picker.startDate.format('YYYY-MM-DD'));
                console.log(picker.endDate.format('YYYY-MM-DD'));
            });
        })
    </script>
    <script>
        var january_orders = {{ $client->january_orders }};
        var february_orders = {{ $client->february_orders }};
        var march_orders = {{ $client->march_orders }};
        var april_orders = {{ $client->april_orders }};
        var may_orders = {{ $client->may_orders }};
        var june_orders = {{ $client->june_orders }};
        var july_orders = {{ $client->july_orders }};
        var august_orders = {{ $client->august_orders }};
        var september_orders = {{ $client->september_orders }};
        var october_orders = {{ $client->october_orders }};
        var november_orders = {{ $client->november_orders }};
        var december_orders = {{ $client->december_orders }};

        var monday = {{ json_encode($statistic_in_day['monday']) }};
        var tuesday = {{ json_encode($statistic_in_day['tuesday']) }};
        var wednesday = {{ json_encode($statistic_in_day['wednesday']) }};
        var thursday = {{ json_encode($statistic_in_day['thursday']) }};
        var friday = {{ json_encode($statistic_in_day['friday']) }};
        var saturday = {{ json_encode($statistic_in_day['saturday']) }};
        var sunday = {{ json_encode($statistic_in_day['sunday']) }};

        const data7 = {
            labels: [
                'Saturday',
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday'
            ],
            datasets: [{
                label: 'My First Dataset',
                data: [saturday, sunday, monday, tuesday, wednesday, thursday, friday],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 85)',
                    'rgb(265, 69, 14)',
                    'rgb(84, 72, 35)',
                    'rgb(25, 25, 86)',
                    'rgb(255, 255, 0)',
                    'rgb(200, 72, 235)',
                ],
                hoverOffset: 4
            }]
        };

        const config7 = {
            type: 'pie',
            data: data7,
        };
        const myChart7 = new Chart(
            document.getElementById('myChart7'),
            config7
        );

        const data = {
            labels: [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ],
            datasets: [{
                type: 'bar',
                label: 'Bar',
                data: [january_orders, february_orders, march_orders, april_orders, may_orders, june_orders,
                    july_orders, august_orders, september_orders, october_orders, november_orders,
                    december_orders
                ],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)'
            }, {
                type: 'line',
                label: 'Line',
                data: [january_orders, february_orders, march_orders, april_orders, may_orders, june_orders,
                    july_orders, august_orders, september_orders, october_orders, november_orders,
                    december_orders
                ],
                fill: false,
                borderColor: 'rgb(54, 162, 235)'
            }]
        };


        const config = {
            type: 'scatter',
            data: data,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };
        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
    </script>

@stop
@section('content')
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div style="margin-top: 20px;margin-{{ __('dashboard.right') }}: 50px;float: {{ __('dashboard.right') }};">
                    <div class="content-group">
                        <h5 class="text-semibold no-margin"><i
                                class="glyphicon glyphicon-envelope position-{{ __('dashboard.right') }} text-slate"></i>
                            <span id="sms_balance"></span> {{ __('dashboard.message') }}
                        </h5>
                        {{-- <span class="text-muted text-size-small" id="balance_note"></span> --}}
                    </div>
                </div>

                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-{{ __('dashboard.right') }}"></i> <span
                            class="text-semibold">{{ __('dashboard.home') }}</span> -
                        {{ __('dashboard.dashboard') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb"
                    style="float: {{ __('dashboard.left') }}; margin-{{ __('dashboard.left') }}: 14px; margin-{{ __('dashboard.right') }}: 0;">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-{{ __('dashboard.right') }}"></i>
                            {{ __('dashboard.home') }}</a></li>
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
                            <h6 class="panel-title">{{ __('dashboard.app_stats') }}</h6>
                        </div>

                        <div class="container-fluid">
                            @if (auth()->user()->user_type_id == 1)
                                <?php $privileges = \App\Models\Privileges::where('is_provider', 0)
                                    ->where('model', '!=', '')
                                    ->where('hidden', 0)
                                    ->orderBy('orders', 'ASC')
                                    ->get(); ?>
                            @elseif(Auth::User()->user_type_id == 2 && !empty(Auth::user()->privilege_id))
                                <?php
                                $pr = \App\Models\PrivilegesGroupsDetails::where('privilege_group_id', Auth::user()->privilege_id)
                                    ->pluck('privilege_id')
                                    ->toArray();
                                $privileges = \App\Models\Privileges::whereIn('id', $pr)
                                    ->where('is_provider', 0)
                                    ->where('hidden', 0)
                                    ->where('model', '!=', '')
                                    ->where('parent_id', 0)
                                    ->orderBy('orders', 'ASC')
                                    ->get();
                                ?>
                            @else
                                <?php
                                $privileges = \App\Models\Privileges::where('is_provider', 0)
                                    ->where('model', '!=', '')
                                    ->where('hidden', 0)
                                    ->orderBy('orders', 'ASC')
                                    ->get();
                                ?>
                            @endif
                            @if (count($privileges) > 0)
                                @foreach ($privileges as $privilege)
                                    <div class="col-lg-3 col-xs-6">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua"
                                            style="background-color: {{ $privilege->card_color }} !important;">
                                            <div class="inner">
                                                <h3>@php
                                                    @eval("echo $privilege->model;");
                                                @endphp
                                                </h3>
                                                <p>{{ __($privilege->privilge) }}</p>
                                            </div>
                                            <div class="icon">
                                                <i class="{{ $privilege->icon }}"
                                                    style="float: left;margin-top: 22px;font-size: 50px;"></i>
                                            </div>
                                            <a href="@if ($privilege->url) {{ $privilege->url }}   @else {{ @\App\Models\Privileges::where('parent_id', $privilege->id)->orderBy('id', 'desc')->first()->url }} @endif"
                                                class="small-box-footer">More info <i
                                                    class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div><!-- ./col -->
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /sales stats -->
            </div>
            <div class="col-lg-12 flex">
                <div class="col-lg-4 col-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('dashboard.missing_products') }}</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive text-center">
                                <table class="table no-margin text-center">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.id') }}</th>
                                            <th>{{ __('dashboard.product_name') }}</th>
                                            <th>{{ __('dashboard.quantity') }}</th>
                                            <th>{{ __('dashboard.minimum_quantity_in_warehouse') }}</th>
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
                                                <td colspan="4" class="text-center">
                                                    {{ __('dashboard.not_found_product') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('dashboard.latest_events') }}</h3>
                        </div>
                        <div class="box-body">
                            <ul class="products-list product-list-in-box" style="overflow-y: hidden;overflow-x: hidden;">
                                @php
                                    $notifications = \App\Models\Notification::orderBy('id', 'desc')
                                        ->where('reciever_id', auth()->user()->id)
                                        ->where('message', '!=', '')
                                        ->take(4)
                                        ->get();
                                @endphp
                                @foreach ($notifications as $notify)
                                    <li class="item">
                                        <div class="product-info" style="margin-right: 0px;margin-left: 0px;">
                                            <a href="#"
                                                class="product-title">{{ $notify->created_at->translatedFormat('Y-m-d h:i a') }}
                                            </a>
                                            <span class="product-description">
                                                {{ App::getLocale() == 'en' ? $notify->message_en : $notify->message }}
                                            </span>
                                        </div>
                                    </li><!-- /.item -->
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('dashboard.orders_on_weekdays') }} </h3>
                        </div>
                        <div class="box-body">
                            <canvas id="myChart7"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 flex">
                <div class="col-lg-6 col-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ __('dashboard.sales') }}</h3>
                        </div>
                        <div class="box-body">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title"> {{ __('dashboard.product_orders_in_the_last_24_hours') }}</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive text-center">
                                <table class="table no-margin text-center">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.order_id') }}</th>
                                            <th>{{ __('dashboard.order_owner') }}</th>
                                            <th>{{ __('dashboard.payment_type') }}</th>
                                            <th>{{ __('dashboard.order_status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($last_orders as $order)
                                            <tr>
                                                <td><a href="/admin-panel/orders/{{ $order->id }}"
                                                        target="_blank">{{ $order->id }}</a></td>
                                                <td>{{ @$order->user->username }}</td>
                                                <td>{{ app()->getLocale() == 'ar' ? @$order->paymentMethod->name : @$order->paymentMethod->name_en }}
                                                </td>
                                                <td>{{ app()->getLocale() == 'ar' ? @$order->orderStatus->name : @$order->orderStatus->name_en }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="4" class="text-center">
                                                    {{ __('dashboard.not_found_orders') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div><!-- /.box-body -->
                        <div class="box-footer clearfix">
                            <a href="/admin-panel/orders"
                                class="btn btn-sm btn-default btn-flat pull-right">{{ __('dashboard.view_all') }}</a>
                        </div><!-- /.box-footer -->
                    </div>
                </div>
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
