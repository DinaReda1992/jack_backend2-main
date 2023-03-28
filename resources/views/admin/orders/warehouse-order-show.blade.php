@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
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
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ __('dashboard.warehouse') }}</h4>
                </div>
                <div class="heading-elements">
                    @if ($object->status == 2 && $object->stop == 0 && $object->return_to_wallet == 0)
                        <div class="heading-btn-group">
                            {{-- <a href="/admin-panel/warehouse/edit-order/{{ $object->id }}"><button type="button"
                                    class="btn btn-primary p-2 m-1" name="button">
                                    {{ __('dashboard.edit_order') }}</button></a>
                            <a onclick="return false;" data-toggle="modal" data-target="#stopModal{{ $object->id }}555"
                                href="#"><button type="button" class="btn btn-warning p-2 m-1" name="button">
                                    {{ __('dashboard.pause_order') }}</button></a>
                            <div class="modal fade" id="stopModal{{ $object->id }}555" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">
                                                {{ __('dashboard.pause_order_message') }}</h4>
                                        </div>
                                        <form method="get" action="/admin-panel/warehouse/stop-order/{{ $object->id }}">
                                            <div class="modal-body">
                                                <h3 class="text-center"> {{ __('dashboard.are_you_sure_to_pause_order') }}
                                                </h3>
                                            </div>
                                            <div class="modal-footer">
                                                <a type="button" class="btn btn-default"
                                                    data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                <button type="submit"
                                                    class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> --}}
                            <a onclick="return false;" data-toggle="modal" data-target="#cancelModal{{ $object->id }}555"
                                href="#"><button type="button" class="btn btn-danger p-2 m-1" name="button">
                                    {{ __('dashboard.cancel_order') }}</button></a>
                            <div class="modal fade" id="cancelModal{{ $object->id }}555" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">
                                                {{ __('dashboard.message_cancel_order') }}</h4>
                                        </div>
                                        <form method="get"
                                            action="/admin-panel/warehouse/cancel-order/{{ $object->id }}">
                                            <div class="modal-body">
                                                <h3 class="text-center">
                                                    {{ __('dashboard.are_you_sure_you_want_to_cancel_this_order') }}</h3>
                                            </div>
                                            <div class="modal-footer">
                                                <a type="button" class="btn btn-default"
                                                    data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                <button type="submit"
                                                    class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- <a onclick="return false;" data-toggle="modal"
                                data-target="#returnTowalletModal{{ $object->id }}555" href="#"><button
                                    type="button" class="btn btn-primary p-2 m-1" name="button">
                                    {{ __('dashboard.return_the_order_amount_to_the_wallet') }}</button></a>
                            <div class="modal fade" id="returnTowalletModal{{ $object->id }}555" tabindex="-1"
                                role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">
                                                {{ __('dashboard.message_return_the_order_amount_to_the_wallet') }}</h4>
                                        </div>
                                        <form method="get"
                                            action="/admin-panel/warehouse/return-to-wallet/{{ $object->id }}">
                                            <div class="modal-body">
                                                <h3 class="text-center">
                                                    {{ __('dashboard.are_you_sure_from_return_the_order_amount_to_the_wallet') }}
                                                </h3>
                                            </div>
                                            <div class="modal-footer">
                                                <a type="button" class="btn btn-default"
                                                    data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                <button type="submit"
                                                    class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    @endif
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i>
                            {{ __('dashboard.home') }}</a></li>
                    <li><a href="/admin-panel/warehouse">{{ __('dashboard.warehouse') }}</a></li>
                    <li class="active">{{ __('dashboard.order_id') }} {{ $object->id }} </li>
                </ul>

            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

            @include('admin.message')

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ __('dashboard.order_id') }} {{ $object->id }} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">
                    <div class="w-100" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog1" role="document">
                            <div class="modal-content">
                                <div class="modal-header pb-3">

                                    <h4 class="modal-title pull-{{ __('dashboard.right') }}" id="myModalLabel">
                                        {{ __('dashboard.order_details') }}</h4>
                                    <div class="pull-{{ __('dashboard.left') }}">

                                        @if ($object->status != 5)
                                            {{ __('dashboard.order_status') }}:
                                            <span class=" ">
                                                {{ @$object->orderStatus->name }}
                                            </span>
                                        @endif

                                        @if ($object->status != 7)
                                            @if ($object->stop == 0 && $object->return_to_wallet == 0)
                                                <a onclick="return false;" data-toggle="modal"
                                                    data-target="#4approve_order{{ $object->id }}555" href="#">
                                                    <button type="button" name="button" class="btn btn-primary">
                                                        {{ @$object->orderStatus->btn_text }}
                                                    </button>
                                                </a>
                                                <div class="modal fade" id="4approve_order{{ $object->id }}555"
                                                    tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal" aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    {{ __('dashboard.message_change_status_order') }}</h4>
                                                            </div>
                                                            <form method="get"
                                                                action="/admin-panel/warehouse/approve_order/{{ $object->id }}">
                                                                <div class="modal-body">

                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1"></label>
                                                                        {{ __('dashboard.are_you_sure_from') }}
                                                                        {{ @$object->orderStatus->btn_text }}
                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a type="button" class="btn btn-default"
                                                                        data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                                    <button type="submit"
                                                                        class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif ($object->stop == 1)
                                                <button type="button" name="button" class="btn btn-warning">
                                                    {{ __('dashboard.order_pending') }}</button>
                                            @elseif ($object->return_to_wallet == 1)
                                                <button type="button" name="button" class="btn btn-warning">
                                                    {{ __('dashboard.the_order_amount_has_been_returned_to_the_wallet') }}
                                                </button>
                                            @endif
                                        @endif

                                        @if ($object->status == 7 || $object->status == 2)
                                            <a href="/admin-panel/orders/{{ $object->id }}/edit" target="_blank"
                                                class="btn btn-default "><i class="fa fa-print"></i> Print</a>
                                        @elseif ($object->status == 3 && $object->paid_price > 0)
                                            )
                                            <a href="/admin-panel/orders/{{ $object->id }}/edit" target="_blank"
                                                class="btn btn-default "><i class="fa fa-print"></i> Print</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="modal-body">


                                    <div class="row flex">
                                        <label class="col-sm-4"><strong>{{ __('dashboard.order_owner') }}</strong></label>

                                        <div class="col-sm-12">
                                            <div class="box-body box-profile">
                                                <img class="profile-user-img img-responsive img-circle"
                                                    src="/uploads/{{ @$object->user->photo == '' ?: 'default-user.png' }}"
                                                    style="height: 100px;" alt="User profile picture">
                                                <h3 class="profile-username text-center">{{ @$object->user->username }}
                                                </h3>
                                                <p class="text-muted text-center">{{ @$object->user->phone }}</p>
                                                <div class="text-center">
                                                    @if ($object->sent_sms == 0)
                                                        <p class="text-center">
                                                            {{ __('dashboard.the_invoice_has_been_sent_to_the_customer') }}
                                                        </p>
                                                        <a href="/admin-panel/orders/send-invoice/{{ $object->id }}">
                                                            <button type="button" name="button"
                                                                class="btn btn-primary">
                                                                {{ __('dashboard.send_again') }}
                                                            </button>
                                                        </a>
                                                    @else
                                                        <a href="/admin-panel/orders/send-invoice/{{ $object->id }}">
                                                            <button type="button" name="button"
                                                                class="btn btn-primary">
                                                                {{ __('dashboard.send_the_invoice_to_the_customer') }}
                                                            </button>
                                                        </a>
                                                    @endif
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row flex">
                                        <label class="col-sm-4"><strong>{{ __('dashboard.country') }} /
                                                {{ __('dashboard.city') }} / {{ __('dashboard.street') }}
                                            </strong></label>
                                        <div class="col-sm-8">
                                            <span> {{ __('dashboard.country') }}:{{ @$object->country->name }}</span><br>
                                            <span>{{ __('dashboard.region') }}: {{ @$object->region->name }}</span><br>
                                            <span>{{ __('dashboard.city') }}: {{ @$object->state->name }}</span><br>
                                            <span>{{ __('dashboard.street') }}: {{ @$object->address_name }}</span><br>
                                            <span> {{ @$object->address_desc }}</span><br>
                                            {{-- {{ @$object->county->name.' / '.@$object->region->name.' / '.@$object->state->name }} --}}
                                        </div>

                                    </div>
                                    <hr>

                                    <div class="row flex">
                                        <label class="col-sm-4"><strong> {{ __('dashboard.address_on_map') }}
                                            </strong></label>
                                        <div class="col-sm-8">
                                            <a target="_blank"
                                                href="http://maps.google.com/maps?q={{ @$object->latitude }},{{ @$object->longitude }}">
                                                {{ @$object->address_name }}
                                            </a>
                                        </div>
                                    </div>
                                    <hr>


                                    <div class="row flex">
                                        <label class="col-sm-4"><strong> {{ __('dashboard.payment_type') }}
                                            </strong></label>
                                        @if (!$object->parent_order)

                                            <div class="col-sm-8">
                                                <p>{{ @$object->paymentMethod->name }}</p>
                                                @if (@$object->with_balance == 1)
                                                    <div>
                                                        <span>{{ __('dashboard.pay_with_balance') }}</span>
                                                        <span> {{ @$object->balance->price }}
                                                            {{ __('dashboard.sar') }}</span>
                                                    </div>
                                                @endif
                                                @if ($object->payment_method == 5 || $object->payment_method == 4)
                                                    @if (@$object->transfer_photo->photo != null)
                                                        @if ($object->transfer_photo->photo != '')
                                                            @php
                                                                $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                                                                $contentType = @mime_content_type('uploads/' . @$object->transfer_photo->photo);
                                                                
                                                            @endphp
                                                            @if (in_array($contentType, $allowedMimeTypes))
                                                                <img width="300"
                                                                    src="/uploads/{{ @$object->transfer_photo->photo }}" />
                                                            @else
                                                                <a target="_blank"
                                                                    href="/uploads/{{ @$object->transfer_photo->photo }}">{{ __('dashboard.view_file') }}</a>
                                                            @endif

                                                        @endif
                                                    @endif

                                                    <form
                                                        action="/admin-panel/new-orders/upload-invoice/{{ $object->id }}"
                                                        method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group mt-3">
                                                            <label> {{ __('dashboard.add_payment_receipt') }}</label>
                                                            <input class="form-control" type="file" name="photo" />
                                                        </div>
                                                        <div class="form-group">
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ __('dashboard.save') }}</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <div class="col-sm-8">
                                                <span> {{ __('dashboard.paid_due_to_missing_on_order_no') }} </span>
                                                <a
                                                    href="/admin-panel/new-orders/{{ $object->parent_order }}">{{ $object->parent_order }}</a>
                                                <br>
                                            </div>
                                        @endif
                                    </div>

                                    <hr>

                                    <div class="row flex">
                                        <label class="col-sm-4"><strong>
                                                {{ __('dashboard.order_price') }}</strong></label>
                                        <div class="col-sm-8">
                                            {{ round($object->subtotal + $object->cobon_discount + $object->delivery_price + $object->order_vat, 2) }}
                                            {{ __('dashboard.sar') }}
                                        </div>
                                    </div>
                                    <hr>
                                    @if (@$object->transaction != null)
                                        <div class="row flex">
                                            <label class="col-sm-4"><strong>
                                                    {{ __('dashboard.the_second_installment') }}</strong></label>
                                            <div class="col-sm-8">
                                                @if ($object->transaction->payment_method == 4)
                                                    @php
                                                        $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                                                        $contentType = @mime_content_type('uploads/' . @$object->transaction->getBankTransfer->photo);
                                                        
                                                    @endphp
                                                    <span>{{ __('dashboard.pay_bank_transfer_amount') }}
                                                        {{ @$object->transaction->payed_price }}</span>
                                                    @if (in_array($contentType, $allowedMimeTypes))
                                                        <a data-fancybox data-caption="v">
                                                            <img width="300"
                                                                src="/uploads/{{ @$object->transaction->getBankTransfer->photo }}" /></a>
                                                    @else
                                                        <a target="_blank"
                                                            href="/uploads/{{ @$object->transaction->getBankTransfer->photo }}">{{ __('dashboard.view_file') }}</a>
                                                    @endif
                                                @endif
                                                @if (@$object->transaction->payment_method == 3)
                                                    {{ __('dashboard.pay_with_the_balance') }}
                                                    {{ @$object->transaction->price }}
                                                @endif
                                                @if (@$object->transaction->payment_method == 2)
                                                    {{ __('dashboard.electronic_payment') }}
                                                    {{ @$object->transaction->price }}
                                                @endif


                                            </div>
                                        </div>
                                    @endif
                                    <hr>

                                    @if ($object->scheduling_date != null)
                                        <div class="row flex">
                                            <label class="col-sm-4"><strong>
                                                    {{ __('dashboard.order_type') }}</strong></label>
                                            <div class="col-sm-8">
                                                {{ __('dashboard.scheduled_orders') }}
                                            </div>
                                        </div>
                                        <div class="row flex">
                                            <label class="col-sm-4"><strong>
                                                    {{ __('dashboard.delivery_date') }}</strong></label>
                                            <div class="col-sm-8">
                                                {{ @$object->scheduling_date }}
                                            </div>
                                        </div>
                                    @endif

                                    <hr>
                                    @if ($object->shipments->count() > 0)
                                        <div class="row">
                                            <label class="col-sm-12"><strong>
                                                    {{ __('dashboard.order_details') }}</strong></label>
                                            <div class="col-sm-12">
                                                @foreach ($object->shipments as $key => $shipment)
                                                    <div class=""
                                                        style="display: flex;margin: 20px 0;align-items: center">
                                                        <div>
                                                            <strong> {{ __('dashboard.shipment_id') }} :</strong>
                                                            <strong> {{ $shipment->id }} </strong>
                                                         </div>
                                                       {{-- <div style="margin-right: 20px">
                                                            <strong> {{ __('dashboard.shipment_date') }} :</strong>
                                                            <strong> {{ $shipment->delivery_date }} </strong>
                                                        </div>
                                                        <div style="margin-right: 20px">
                                                            <strong>{{ __('dashboard.merchant') }} :</strong>
                                                            <strong>{{ @$shipment->shop->username }}</strong>
                                                        </div> --}}
                                                        <div style="margin-right: 20px">
                                                            @if ($shipment->status != 5)
                                                                {{ __('dashboard.shipment_status') }}:
                                                                <span class=" ">
                                                                    {{ @$shipment->orderStatus->name }}
                                                                </span>
                                                            @endif

                                                            @if ($shipment->status != 7)
                                                                @if ($object->stop == 0 && $object->return_to_wallet == 0)
                                                                    <a onclick="return false;" data-toggle="modal"
                                                                        data-target="#6approve_order{{ @$shipment->id }}555"
                                                                        href="#">
                                                                        <button type="button" name="button"
                                                                            class="btn btn-primary">
                                                                            {{ @$shipment->orderStatus->btn_text }}
                                                                        </button>
                                                                    </a>
                                                                    <div class="modal fade"
                                                                        id="6approve_order{{ $shipment->id }}555"
                                                                        tabindex="-1" role="dialog"
                                                                        aria-labelledby="myModalLabel">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <button type="button" class="close"
                                                                                        data-dismiss="modal"
                                                                                        aria-label="Close"><span
                                                                                            aria-hidden="true">&times;</span>
                                                                                    </button>
                                                                                    <h4 class="modal-title"
                                                                                        id="myModalLabel">
                                                                                        {{ __('dashboard.message_change_status_shipment') }}
                                                                                    </h4>
                                                                                </div>
                                                                                <form method="get"
                                                                                    action="/admin-panel/warehouse/approved_shipment/{{ $shipment->id }}">
                                                                                    <div class="modal-body">

                                                                                        <div class="form-group">
                                                                                            <label
                                                                                                for="exampleInputEmail1"></label>
                                                                                            {{ __('dashboard.are_you_sure_from') }}
                                                                                            {{ @$shipment->orderStatus->btn_text }}

                                                                                        </div>

                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <a type="button"
                                                                                            class="btn btn-default"
                                                                                            data-dismiss="modal">{{ __('dashboard.close') }}</a>
                                                                                        <button type="submit"
                                                                                            class="btn btn-primary">{{ __('dashboard.accept') }}</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @elseif ($object->stop == 1)
                                                                    <button type="button" name="button"
                                                                        class="btn btn-warning">
                                                                        {{ __('dashboard.order_pending') }}
                                                                    </button>
                                                                @elseif ($object->return_to_wallet == 1)
                                                                    <button type="button" name="button"
                                                                        class="btn btn-warning">
                                                                        {{ __('dashboard.the_order_amount_has_been_returned_to_the_wallet') }}
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <table class="table table-bordered text-center">
                                                        <tr style="background: #e8e8e8">
                                                            <th>{{ __('dashboard.product') }}</th>
                                                            <th>{{ __('dashboard.quantity') }}</th>
                                                            <th>{{ __('dashboard.quantity_in_warehouse') }}</th>
                                                            <th>{{ __('dashboard.unit_price') }}</th>
                                                            <th>{{ __('dashboard.total') }}</th>

                                                        </tr>
                                                        @php	$total_shipment_price=0;@endphp
                                                        @foreach ($shipment->cart_items as $key2 => $item)
                                                            @php $total_shipment_price=$total_shipment_price+$item->price*$item->quantity @endphp
                                                            <tr>
                                                                <td>{{ @$item->product->title }}</td>
                                                                <td>{{ @$item->quantity }} </td>
                                                                <td>{{ @$item->product->quantity - @$item->product->min_warehouse_quantity }}
                                                                </td>
                                                                <td>{{ @$item->price }} {{ __('dashboard.sar') }} </td>
                                                                <td>{{ @$item->price * $item->quantity }}
                                                                    {{ __('dashboard.sar') }} </td>

                                                            </tr>
                                                        @endforeach

                                                        <tr style="background: #daefff">
                                                            <td colspan="4">{{ __('dashboard.total') }}</td>
                                                            <td>{{ $total_shipment_price }} {{ __('dashboard.sar') }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                @endforeach

                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-12"><strong> </strong></label>
                                            <div class="col-sm-12">
                                                <h3>{{ __('dashboard.summary') }}</h3>
                                                <table class="table table-bordered text-center">
                                                    <tr style="background: #e8e8e8">
                                                        <td colspan="3">{{ __('dashboard.products_price') }}</td>
                                                        <td>{{ @round($object->subtotal, 2) }} {{ __('dashboard.sar') }}
                                                        </td>
                                                    </tr>
                                                    <tr style="background: #e8e8e8">
                                                        <td colspan="3">{{ __('dashboard.delivery_price') }}</td>
                                                        <td>{{ $object->delivery_price }} {{ __('dashboard.sar') }}</td>
                                                    </tr>
                                                    @if ($object->cobon_discount > 0)
                                                        <tr style="background: #e8e8e8">
                                                            <td colspan="3">{{ __('dashboard.discount_coupon') }} ({{__('dashboard.from_ship')}})</td>
                                                            <td>- {{ $object->cobon_discount }} %
                                                                ({{ ($object->cobon_discount * $object->delivery_price) / 100 }}
                                                                {{ __('dashboard.sar') }}) </td>
                                                        </tr>
                                                    @endif
                                                    <tr style="background: #e8e8e8">
                                                        <td colspan="3">{{ __('dashboard.sub_total') }}</td>
                                                        <td><strong>{{ round($object->order_price - $object->cobon_discount + $object->delivery_price, 2) }}
                                                                {{ __('dashboard.sar') }}</strong> </td>
                                                    </tr>

                                                    @if ($object->taxes > 0)
                                                        <tr style="background: #e8e8e8">
                                                            <td colspan="3">{{ __('dashboard.tax') }}</td>
                                                            <td> {{ @round($object->taxes, 2) }}
                                                                {{ __('dashboard.sar') }}</td>
                                                        </tr>
                                                    @endif
                                                    <tr style="background: #daefff">
                                                        <td colspan="3">{{ __('dashboard.total') }}</td>
                                                        <td>
                                                            {{ round($object->final_price, 2) }}
                                                            {{ __('dashboard.sar') }}</td>
                                                    </tr>
                                                    @if (@$object->balance()->whereNotIn('balance_type_id', [15, 16])->first())
                                                        <tr style="background: #e8e8e8">
                                                            <td colspan="3">{{ __('dashboard.balance_deduction') }}
                                                            </td>
                                                            <td><strong>{{ @$object->balance()->whereNotIn('balance_type_id', [15, 16])->first()->price }}
                                                                    {{ __('dashboard.sar') }} </strong></td>
                                                        </tr>
                                                        <tr style="background: #e8e8e8">
                                                            <td colspan="3">{{ __('dashboard.total') }} </td>
                                                            <td><strong>{{ @round($object->final_price + @$object->balance->price, 2) }}
                                                                    {{ __('dashboard.sar') }}</strong></td>
                                                        </tr>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                        <hr>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
@stop
