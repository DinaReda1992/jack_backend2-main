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
                        {{ __('dashboard.view') }} {{ $type }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }} </a>
                    </li>
                    <li><a href="/admin-panel/all-users/user-page/{{ $user->id }}">{{ $user->username }}</a></li>
                    <li class="active"><a href="">{{ __('dashboard.view') }} {{ $type }}</a></li>
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
                    <h5 class="panel-title">{{ __('dashboard.view') }} {{ $type }}</h5>
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

                                {{--								<li class="{{ !request()->status?"active":""}}"> --}}
                                {{--									<a href="/admin-panel/warehouse">الكل</a></li> --}}
                                <li class="{{ request()->status == 2 ? 'active' : '' }}">
                                    <a href="/admin-panel/warehouse?status=2">قيد التجهيز</a>
                                </li>
                                <li class="{{ request()->status == 3 ? 'active' : '' }}">
                                    <a href="/admin-panel/warehouse?status=3">{{ __('dashboard.shipping') }}</a>
                                </li>
                                <li class="{{ request()->status == 4 ? 'active' : '' }}">
                                    <a href="/admin-panel/warehouse?status=4">{{ __('dashboard.shipped') }}</a>
                                </li>
                                <li class="{{ request()->status == 6 ? 'active' : '' }}">
                                    <a href="/admin-panel/warehouse?status=6">{{ __('dashboard.delivering') }}</a>
                                </li>
                                <li class="{{ request()->status == 7 ? 'active' : '' }}">
                                    <a href="/admin-panel/warehouse?status=7">{{ __('dashboard.completed') }}</a>
                                </li>

                            </ul>
                        </div>
                    @endif

                </div>

                <div class="clearfix"></div>
                <br>




                <div class="table-responsive text-center text-center">

                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>{{ __('dashboard.order_id') }}</th>
                                <th style="width: 200px">{{ __('dashboard.order_date_creation') }}</th>
                                <th style="width: 200px">{{ __('dashboard.order_time_creation') }}</th>
                                <th>{{ __('dashboard.client_name') }}</th>
                                {{-- <th> {{ __('dashboard.supplier') }}</th> --}}
                                @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                                    <th>
                                        {{ __('dashboard.employee_name') }}
                                    </th>
                                    <th>
                                        {{ __('dashboard.marketers_name') }}
                                    </th>
                                @endif
                                <th>
                                    {{ __('dashboard.region') }}
                                </th>
                                <th>
                                    {{ __('dashboard.receipt_attached') }}
                                </th>
                                <th style="width: 127px">{{ __('dashboard.order_details') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($objects as $object)
                                <tr parent_id="{{ $object->id }}">

                                    <td>{{ $object->id }}</td>
                                    @if ($object->status == 0)
                                        <td>{{ $object->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $object->created_at->diffForHumans() }}</td>
                                    @elseif($object->status == 1)
                                        {{--                <td>{{\Carbon\Carbon::parse( $object->marketed_date)->format('Y-m-d h:i a')}}</td> --}}
                                        {{--                <td>{{\Carbon\Carbon::parse( $object->marketed_date)->diffForHumans()}}</td> --}}
                                        <td>{{ $object->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $object->created_at->diffForHumans() }}</td>
                                    @elseif($object->status >= 2)
                                        <td>{{ $object->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $object->created_at->diffForHumans() }}</td>

                                        {{--                <td>{{\Carbon\Carbon::parse( $object->financial_date)->format('Y-m-d')}}</td> --}}
                                        {{--                <td>{{\Carbon\Carbon::parse( $object->financial_date)->diffForHumans()}}</td> --}}
                                    @endif
                                    <td>
                                        <a
                                            href="{{ '/admin-panel/all-users/' . @$object->user->id . '/edit' }}">{{ @$object->user->username }}</a>
                                    </td>
                                    {{-- <td>
                                        <a
                                            href="{{ '/admin-panel/suppliers/' . @$object->provider->id . '/edit' }}">{{ @$object->provider->username }}</a>
                                    </td> --}}

                                    @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                                        <td>
                                            @if ($object->status == 0)
                                                <a
                                                    href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                                        </td>
                                    @elseif($object->status == 1)
                                        <a
                                            href="{{ '/admin-panel/all-users/' . @$object->accepted->id . '/edit' }}">{{ @$object->accepted->username }}</a>
                                        </td>
                                    @elseif($object->status >= 2)
                                        <a
                                            href="{{ '/admin-panel/all-users/' . @$object->reviewd->id . '/edit' }}">{{ @$object->reviewd->username }}</a>
                                        </td>
                                    @endif
                                    </td>
                            @endif
                            @if (auth()->user()->user_type_id == 1 && \Request::segment(2) != 'orders')
                                <td>
                                    <a
                                        href="{{ '/admin-panel/all-users/' . @$object->added->id . '/edit' }}">{{ @$object->added->username }}</a>
                                </td>

                                </td>
                            @endif
                            <td>
                                {{ @$object->region->name }}
                            </td>
                            <td>
                                @if (@$object->payment_method == 3)
                                    <span class="badge badge-success">{{ __('dashboard.pay_with_the_balance') }}</span>
                                @elseif($object->payment_method == 2)
                                    <span class="badge badge-success">{{ __('dashboard.electronic_payment') }}</span>
                                @else
                                    @if (@$object->transfer_photo->photo != '')
                                        <span class="badge badge-success">{{ __('dashboard.receipt_attached') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('dashboard.unpaid') }}</span>
                                    @endif
                                @endif
                                {{--  @if (@$object->transfer_photo->photo != '')
                                        <span class="badge badge-success">{{__('dashboard.receipt_attached')}}</span>
                                    @else
                                        <span class="badge badge-danger">{{__('dashboard.unpaid')}}</span>

                                    @endif --}}
                            </td>
                            <td>
                                @if ($object->status == 0)
                                    <a href="{{ url('admin-panel/orders/' . $object->id) }}">
                                        {{ __('dashboard.details') }}
                                        ( {{ @$object->cart_items->count() }} )
                                    </a>
                                @elseif($object->status == 1)
                                    <a href="{{ url('admin-panel/orders/' . $object->id) }}">
                                        {{ __('dashboard.details') }}
                                        ( {{ @$object->cart_items->count() }} )
                                    </a>
                                @elseif($object->status >= 2)
                                    <a href="{{ url('admin-panel/orders/' . $object->id) }}">
                                        {{ __('dashboard.details') }}
                                        ( {{ @$object->cart_items->count() }} )
                                    </a>
                                @endif
                                {{-- <a href="{{url('admin-panel/orders/'.$object->id)}}"
                                     > {{__('dashboard.details')}} ( {{ @$object->cart_items->count() }} )
                                     </a> --}}

                            </td>

                            </tr>
                            @endforeach
                            @if (count($objects) == 0)
                                <tr>
                                    <td colspan="11" align="center">{{ __('dashboard.not_found_orders') }}</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>

                    <div class="clearfix"></div>
                    <br>
                    <hr>
                    <div align="center">
                        {{ $objects->appends(Request::except('page'))->links() }}

                        {{--    {!! $objects->render() !!} --}}
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
    <script type="text/javascript">
        // Warning alert
    </script>

@stop
