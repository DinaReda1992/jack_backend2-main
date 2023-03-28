<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Jak App | Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link href="/assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/AdminLTE-RTL.min.css" rel="stylesheet" type="text/css">
    <style>
        @media (max-width: 575.98px) {
            .table-responsive text-center-sm {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        th {
            text-align: right;
            padding: 5px
        }

        .invoice-info {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
        }

        .page-header {
            border: none;
            margin-bottom: 0;
        }

        td img {
            width: 50px;
        }

        @media print {

            .no-print {
                display: none;
            }

            td {
                padding: 0 3px !important;
            }

            td img {
                padding: 5px !important;
                width: 30px;
            }

            th {
                padding: 5px !important;
                font-size: 10px;
            }
        }
    </style>
</head>
{{-- <body onload="window.print();"> --}}

<body>
    <div class="wrapper">

        <section class="invoice">
            <!-- title row -->
            <div class="no-print text-center">
                <a class="btn btn-success" onclick="window.print();">Print</a>
            </div>
            <div class="text-center">
                <img src="/images/{{ \App\Models\Settings::find(46)->value }}" style="width: 100%" />

                {{-- <span>
                    <strong>VAT : 310414274700003</strong>
                </span> --}}
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header">
                        <i class="fa fa-globe"></i>
                        {{ __('dashboard.order_id') }}
                        <strong style="color: blue;display:inline-block">#{{ $object->id }}</strong>
                        <small class="pull-left">{{ __('dashboard.date') }}:
                            {{ $object->updated_at->format('Y-m-d h:i A') }}</small>
                    </div>
                </div><!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    <b>{{ __('dashboard.client') }}</b><br>

                    <address>
                        <strong> {{ __('dashboard.client_name') }}:{{ @$object->user->username }}</strong><br>
                        <strong>{{ __('dashboard.phone') }}: {{ '0' . @$object->user->phone }} </strong> <br>
                        @if (@$object->user->email)
                            <strong> {{ __('dashboard.email') }}: {{ $object->user->email }} </strong><br>
                        @endif
                        <strong>{{ __('dashboard.tax_number') }}:{{ @$object->user->tax_number }}</strong><br>
                    </address>
                </div><!-- /.col -->

                <div class="col-sm-4 invoice-col">
                    <b>{{ __('dashboard.address') }}</b><br>
                    <address>
                        {{-- <span>{{ __('dashboard.country') }}:{{ @$object->country->name }}</span><br> --}}
                        <span>{{ __('dashboard.region') }}: {{ @$object->region->name }}</span><br>
                        <span>{{ __('dashboard.city') }}: {{ @$object->state->name }}</span><br>
                        <span>{{ __('dashboard.street') }}: {{ @$object->address_name }}</span><br>
                        <span> {{ @$object->address_desc }}</span><br>
                        <br>
                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <b>{{ __('dashboard.order_data') }}</b><br>
                    <span> {{ __('dashboard.order_id') }}:<strong>{{ @$object->id }}</strong></span><br>
                    @if ($object->payment_method == 0)
                        <span> {{ __('dashboard.order_status') }}: غير مؤكد</span><br>
                        <span>
                            {{ __('dashboard.payment_type') }}:{{ @$object->cartPaymentMethod != null ? @$object->cartPaymentMethod->name : @$object->cartPaymentMethod->name }}</span><br>
                    @else
                        <span> {{ __('dashboard.payment_type') }}:{{ @$object->paymentMethod->name }}</span><br>
                    @endif
                    @if (@$object->scheduling_date != null)
                        <span> {{ __('dashboard.delivery_date') }}:{{ @$object->scheduling_date }}</span><br>
                    @endif
                    <div class="clearfix" style="margin-bottom: 10px;"></div>
                    {!! QrCode::size(100)->encoding('UTF-8')->generate($qr_signature) !!}


                </div><!-- /.col -->
            </div><!-- /.row -->

            @if ($object->cart_items)
                <div class="row">
                    <label class="col-sm-12"><strong> {{ __('dashboard.order_details') }}</strong></label>
                    <div class="col-sm-12">
                        <div class="table-responsive text-center text-center-sm">
                            <table class="table table-bordered text-center">
                                <tr style="background: #e8e8e8">
                                    <th>{{ __('dashboard.id') }}</th>
                                    <th>{{ __('dashboard.product') }}</th>
                                    <th>{{ __('dashboard.product_name') }}</th>
                                    <th>{{ __('dashboard.quantity') }}</th>
                                    <th>{{ __('dashboard.unit_price') }}</th>
                                    <th>{{ __('dashboard.total') }}</th>

                                </tr>
                                @foreach ($object->cart_items as $key => $item)
                                    <tr>
                                        <td>{{ @$item->product->id }}</td>
                                        <td><img src="/uploads/{{ @$item->product->photo }}"></td>
                                        <td>{{ @$item->product->title }}</td>
                                        <td>{{ @$item->quantity }} </td>
                                        <td><strong>{{ @$item->price }} {{ __('dashboard.sar') }}</strong></td>
                                        <td><strong>{{ @$item->price * $item->quantity }}
                                                {{ __('dashboard.sar') }}</strong></td>

                                    </tr>
                                @endforeach

                                <tr style="background: #daefff">
                                    <td colspan="5">{{ __('dashboard.products_price') }}</td>
                                    <td><strong>{{ round($object->order_price, 2) }}
                                            {{ __('dashboard.sar') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                    <div class="col-sm-12">
                        <div class="table-responsive text-center text-center-sm">
                            <table class="table table-bordered text-center">
                                <tr style="background: #e8e8e8">
                                    <td colspan="5">{{ __('dashboard.products_price') }}</td>
                                    <td><strong>{{ @round($object->order_price, 2) }}
                                            {{ __('dashboard.sar') }}</strong> </td>
                                </tr>
                                <tr style="background: #e8e8e8">
                                    <td colspan="5">{{ __('dashboard.delivery_price') }}</td>
                                    <td><strong>{{ $object->delivery_price }}
                                            {{ __('dashboard.sar') }}</strong> </td>
                                </tr>
                                @if ($object->cobon_discount > 0)
                                    <tr style="background: #e8e8e8">
                                        <td colspan="5">{{ __('dashboard.discount_coupon') }} </td>
                                        <td><strong>- {{ $object->cobon_discount }} {{ __('dashboard.sar') }}</strong>
                                        </td>
                                    </tr>
                                @endif
                                @if ($object->discounts > 0)
                                    <tr style="background: #e8e8e8">
                                        <td colspan="5">{{ __('dashboard.discount') }} </td>
                                        <td><strong>-{{ $object->discounts }} {{ __('dashboard.sar') }}</strong>
                                        </td>
                                    </tr>
                                @endif

                                <tr style="background: #e8e8e8">
                                    <td colspan="5">{{ __('dashboard.sub_total') }}</td>
                                    <td><strong>{{ round($object->order_price - $object->discounts - $object->cobon_discount + $object->delivery_price, 2) }}
                                            {{ __('dashboard.sar') }}</strong> </td>
                                </tr>
                                @if ($object->hand_delivery_fees > 0)
                                    <tr style="background: #e8e8e8">
                                        <td colspan="5">{{ __('dashboard.Payment fees upon receipt') }}</td>
                                        <td> <strong>{{ round($object->hand_delivery_fees, 2) }}
                                                {{ __('dashboard.sar') }} </strong></td>
                                    </tr>
                                @endif
                                @if ($object->taxes > 0)
                                    <tr style="background: #e8e8e8">
                                        <td colspan="5">{{ __('dashboard.tax') }}</td>
                                        <td> <strong>{{ @round($object->taxes, 2) }} {{ __('dashboard.sar') }}
                                            </strong></td>
                                    </tr>
                                @endif
                                <tr style="background: #daefff">
                                    <td colspan="5">{{ __('dashboard.total') }}</td>
                                    <td><strong>
                                            {{ round($object->final_price, 2) }}
                                            {{ __('dashboard.sar') }}</strong> </td>
                                </tr>

                                @if (@$object->balance != null && @$object->balance->balance_type_id != 15)
                                    <tr style="background: #e8e8e8">
                                        <td colspan="5">{{ __('dashboard.discount_from_balance') }} </td>
                                        <td><strong>{{ @$object->balance->price }} {{ __('dashboard.sar') }}</strong>
                                        </td>
                                    </tr>
                                @endif
                                @if (@$object->balance != null && @$object->balance->balance_type_id != 15)
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
            @endif



            <div>
                {{--        <img src="/images/footer.PNG" style="width: 100%" /> --}}
                <img src="/images/{{ \App\Models\Settings::find(47)->value }}" style="width: 100%" />
            </div>
            <!-- this row will not appear when printing -->
        </section><!-- /.content -->
    </div>
</body>

</html>
