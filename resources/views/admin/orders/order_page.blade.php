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
    <div class="content-wrapper" style="min-height: 1096px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="page-header">
                <div class="page-header-content">
                </div>

                <div class="breadcrumb-line">
                    <ul class="breadcrumb">
                        <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i>
                                {{ __('dashboard.home') }}</a></li>
                        <li><a href="/admin-panel/orders">{{__('dashboard.view_orders')}}</a></li>
                        <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} المندوب للطلب</li>
                    </ul>
                </div>
            </div>


        </section>
        @include('admin.message')

        <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <i class="fa fa-globe"></i>
                        {{ __('dashboard.order_id') }}
                        <span style="color: blue">{{ $object->id }}# </span>
                        <span style="color: {{ @$object->orderStatus->color }};font-size: 15px;">
                            (
                            {{ @$object->orderStatus->name . ' ' . ($object->driver ? ': ' . $object->driver->username : '') }}
                            )

                        </span>

                        <small class="pull-right">Date: {{ $object->date }}</small>
                    </h2>
                </div><!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    العميل
                    <address>
                        <strong>{{ $object->user->username }}</strong><br>
                        جوال: {{ $object->user->phone }}<br>
                        @if ($object->user->email)
                            ايميل: {{ $object->user->email }}<br>
                        @endif

                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    العنوان
                    <address>
                        <strong>{{ @$object->state->name }}</strong><br>
                        {{ @$object->district->name }}<br>
                        <a target="_blank"
                            href="http://maps.google.com/maps?q={{ @$object->latitude }},{{ @$object->longitude }}">
                            {{ $object->address }}
                        </a>
                        <br>
                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <b>{{__('dashboard.order_data')}}</b><br>
                    <br>
                    <b>{{ __('dashboard.order_id') }}:</b> {{ $object->id }}<br>
                    <b>يوم
                        التوصيل:</b>
                    {{ trans('dates.' . \Carbon\Carbon::createFromFormat('Y-m-d', $object->date)->format('l')) }}
                    ({{ $object->date }})<br>
                    <b>وقت التسليم:</b> من ({{ $object->from_time }}) الى ( {{ $object->to_time }})
                </div><!-- /.col -->
            </div><!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-xs-12 table-responsive text-center">
                    <p class="lead" style="text-align: right">{{__('dashboard.order_details')}}</p>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{__('dashboard.product')}}</th>
                                <th>{{__('dashboard.quantity')}}</th>
                                <th>{{__('dashboard.price')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($object->cart_items as $detail)
                                <tr>
                                    <td>{{ $detail->type == 1 ? @$detail->itemProduct->title : $detail->itemProduct->part->part_name }}
                                    </td>
                                    <td>{{ $detail->quantity }} </td>

                                    <td>{{ $detail->price }} {{__('dashboard.sar')}}</td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.col -->
            </div><!-- /.row -->

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                    <p class="lead">المندوب:</p>
                    <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                        @if ($object->status != 4)
                            <form method="post" class="form-horizontal"
                                action="{{ '/admin-panel/send-driver/' . $object->id }}">
                                {!! csrf_field() !!}

                                <div class="form-group{{ $errors->has('driver_id') ? ' has-error' : '' }}">

                                    <select name="driver_id" class="form-control">
                                        <option value="">اختر المندوب</option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}"
                                                {{ isset($object) && $object->driver_id == $driver->id ? 'selected' : (old('driver_id') == $driver->id ? 'selected' : '') }}>
                                                {{ $driver->username }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('driver_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('driver_id') }}</strong>
                                        </span>
                                    @endif


                                </div>
                                <button class="btn btn-success ">
                                    {{__('dashboard.send')}}
                                    <i class="fa  fa-truck"></i>
                                </button>
                            </form>
                        @elseif($object->driver_id)
                            <span>
                                {{ @$object->driver->username }}
                            </span>
                        @else
                            لم يتم تحديده بعد
                        @endif

                    </div>
                </div><!-- /.col -->
                <div class="col-xs-6">
                    <p class="lead">الملخص</p>
                    <div class="table-responsive text-center text-center">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:50%">{{__('dashboard.delivery_price')}}:</th>
                                    <td>{{ $object->delivery_price }} {{__('dashboard.sar')}}</td>
                                </tr>
                                @if ($object->cobon_discount > 0)
                                    <tr style="background: #e8e8e8;width:50%">
                                        <th>{{__('dashboard.discount_coupon')}} (من الشحن )</th>
                                        <td>- {{ $object->cobon_discount }} %
                                            ({{ ($object->cobon_discount * $object->delivery_price) / 100 }} {{__('dashboard.sar')}}) </td>
                                    </tr>
                                @endif
                                @if ($object->taxes > 0)
                                    <tr style="background: #e8e8e8;width:50%">
                                        <th colspan="3">{{__('dashboard.tax')}}</th>
                                        <td> {{ $object->taxes }} {{__('dashboard.sar')}}</td>
                                    </tr>
                                @endif
                                @if ($object->extra_service > 0)
                                    <tr style="background: #e8e8e8;width:50%">
                                        <th>خدمة تركيب</th>
                                        <td> {{ $object->extra_service }} {{__('dashboard.sar')}}</td>
                                    </tr>
                                @endif
                                @if ($object->night_cost > 0)
                                    <tr style="background: #e8e8e8;width:50%">
                                        <th>خدمة مسائية</th>
                                        <td> {{ $object->night_cost }} {{__('dashboard.sar')}}</td>
                                    </tr>
                                @endif


                                <tr style="background: #daefff;width:50%">
                                    <th>{{__('dashboard.total')}}</th>
                                    <td>{{ $object->final_price }} {{__('dashboard.sar')}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
                <div class="col-xs-12">

                    <a href="/admin-panel/invoice-print/{{ $object->id }}" target="_blank"
                        class="btn btn-default pull-right"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
        </section><!-- /.content -->
        <div class="clearfix"></div>
    </div>
    <!-- /main content -->
    <script type="text/javascript">
        // Warning alert
    </script>

@stop
