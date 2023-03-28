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

            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
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

                                    <h4 class="modal-title pull-left" id="myModalLabel">{{ __('dashboard.order_details') }}
                                    </h4>
                                    <div class="pull-right">

                                        @if ($object->status != 5)
                                            {{ __('dashboard.order_status') }}:
                                            <span class=" ">
                                                {{ @$object->orderStatus->name }}
                                            </span>
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
                                        <div class="col-sm-8">
                                            <p>{{ @$object->paymentMethod->name }}</p>
                                            @if (@$object->with_balance == 1)
                                                <div>
                                                    <span>{{ __('dashboard.pay_with_balance') }}</span>
                                                    <span> {{ $object->balance->price }} {{ __('dashboard.sar') }}</span>
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

                                                <form action="/admin-panel/new-orders/upload-invoice/{{ $object->id }}"
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
                                    </div>

                                    <hr>

                                    <div class="row flex">
                                        <label class="col-sm-4"><strong> {{ __('dashboard.order_price') }}</strong></label>
                                        <div class="col-sm-8">
                                            {{ round($object->subtotal + $object->cobon_discount + $object->delivery_price + $object->order_vat, 2) }}
                                            {{ __('dashboard.sar') }}
                                        </div>
                                    </div>
                                    <hr>
                                    @if (@$object->transaction != null)
                                        <div class="row">
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
                                                        <a data-fancybox
                                                            data-caption="{{ __('dashboard.transfer_image') }}">
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
                                        <div class="row">
                                            <label class="col-sm-4"><strong>
                                                    {{ __('dashboard.order_type') }}</strong></label>
                                            <div class="col-sm-8">
                                                {{ __('dashboard.scheduled_orders') }}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="col-sm-4"><strong>
                                                    {{ __('dashboard.delivery_date') }}</strong></label>
                                            <div class="col-sm-8">
                                                {{ @$object->scheduling_date }}
                                            </div>
                                        </div>
                                    @endif

                                    <hr>
                                    @if ($object->cart_items)
                                        <form method="POST"
                                            action="/{{ app()->getLocale() }}/admin-panel/warehouse/update-order/{{ $object->id }}">
                                            @csrf
                                            <div class="row">
                                                <label class="col-sm-12"><strong>
                                                        {{ __('dashboard.order_details') }}</strong></label>
                                                <div class="col-sm-12">
                                                    <div class="table-responsive text-center text-center-sm">
                                                        <table class="table table-bordered text-center text-center">
                                                            <tr style="background: #e8e8e8">
                                                                <th>{{ __('dashboard.id') }}</th>
                                                                <th>{{ __('dashboard.product') }}</th>
                                                                <th>{{ __('dashboard.product_name') }}</th>
                                                                <th>{{ __('dashboard.quantity') }}</th>
                                                                <th>{{ __('dashboard.quantity_deleted') }}</th>
                                                                <th>{{ __('dashboard.unit_price') }}</th>
                                                                <th>{{ __('dashboard.total') }}</th>
                                                            </tr>
                                                            @foreach ($object->cart_items as $key => $item)
                                                                <tr>
                                                                    <td>{{ @$item->product->id }}</td>
                                                                    <td><img src="/uploads/{{ @$item->product->photo }}"
                                                                            width="50px" height="50px"
                                                                            class='img-circle'>
                                                                    </td>
                                                                    <td>{{ @$item->product->title }}</td>
                                                                    <td><input type="number" class="form-control"
                                                                            name="{{ $item->id }}" min="0"
                                                                            max="{{ $item->quantity }}"
                                                                            value="{{ $item->quantity }}">
                                                                    </td>
                                                                    <td>
                                                                        {{ $item->quantity_difference }}

                                                                    </td>
                                                                    <td><strong>{{ @$item->price }}
                                                                            {{ __('dashboard.sar') }}</strong></td>
                                                                    <td><strong>{{ @$item->price * $item->quantity }}
                                                                            {{ __('dashboard.sar') }}
                                                                        </strong></td>

                                                                </tr>
                                                            @endforeach

                                                            <tr style="background: #daefff">
                                                                <td colspan="6">{{ __('dashboard.products_price') }}
                                                                </td>
                                                                <td><strong>{{ round($object->order_price, 2) }}
                                                                        {{ __('dashboard.sar') }}
                                                                    </strong>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="table-responsive text-center text-center-sm">
                                                        <table class="table table-bordered text-center">
                                                            <tr style="background: #e8e8e8">
                                                                <td colspan="5">{{ __('dashboard.products_price') }}
                                                                </td>
                                                                <td><strong>{{ @round($object->order_price, 2) }}
                                                                        {{ __('dashboard.sar') }}</strong>
                                                                </td>
                                                            </tr>
                                                            <tr style="background: #e8e8e8">
                                                                <td colspan="5">{{ __('dashboard.delivery_price') }}
                                                                </td>
                                                                <td><strong>{{ $object->delivery_price }}
                                                                        {{ __('dashboard.sar') }}</strong>
                                                                </td>
                                                            </tr>
                                                            @if ($object->cobon_discount > 0)
                                                                <tr style="background: #e8e8e8">
                                                                    <td colspan="5">
                                                                        {{ __('dashboard.discount_coupon') }} </td>
                                                                    <td><strong>{{ $object->cobon_discount }}
                                                                            {{ __('dashboard.sar') }}
                                                                        </strong>
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            <tr style="background: #e8e8e8">
                                                                <td colspan="5">{{ __('dashboard.sub_total') }}</td>
                                                                <td><strong>{{ round($object->order_price - $object->cobon_discount + $object->delivery_price, 2) }}
                                                                        {{ __('dashboard.sar') }}</strong> </td>
                                                            </tr>
                                                            @if ($object->taxes > 0)
                                                                <tr style="background: #e8e8e8">
                                                                    <td colspan="5">{{ __('dashboard.tax') }}</td>
                                                                    <td> <strong>{{ @round($object->taxes, 2) }}
                                                                            {{ __('dashboard.sar') }}
                                                                        </strong>
                                                                    </td>
                                                                </tr>
                                                            @endif

                                                            <tr style="background: #daefff">
                                                                <td colspan="5">{{ __('dashboard.total') }}</td>
                                                                <td><strong>
                                                                        {{ round($object->final_price, 2) }}
                                                                        {{ __('dashboard.sar') }}</strong> </td>
                                                            </tr>

                                                            @if (@$object->balance != null)
                                                                <tr style="background: #e8e8e8">
                                                                    <td colspan="5">
                                                                        {{ __('dashboard.discount_from_balance') }} </td>
                                                                    <td><strong>{{ @$object->balance->price }}
                                                                            {{ __('dashboard.sar') }}
                                                                        </strong>
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                            @if (@$object->balance != null)
                                                                <tr style="background: #e8e8e8">
                                                                    <td colspan="5">{{ __('dashboard.total') }} </td>
                                                                    <td><strong>{{ @round($object->final_price + @$object->balance->price, 2) }}
                                                                            {{ __('dashboard.sar') }}</strong></td>
                                                                </tr>
                                                            @endif

                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row text-center p-3">
                                                <input type="submit" class="btn btn-primary"
                                                    value="{{ __('dashboard.edit_and_preparing_the_order_for_ship') }}">
                                            </div>
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
