<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Golden roads App | Invoice</title>
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

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->
    <style>
        th {
            text-align: right;
            padding:5px
        }

        @media print {

            .no-print {
                display: none;
            }
            td {
                padding:0 5px !important;
            }
            th {
                padding:5px!important;
                font-size: 10px;
            }
        }
    </style>
</head>
{{--<body onload="window.print();">--}}
<body >
<div class="wrapper">

    <section class="invoice">
        <!-- title row -->
        <div class="no-print text-center">
            <a class="btn btn-success" onclick="window.print();">Print</a>
        </div>
        <div class="text-center">
            <img src="/images/header.PNG" style="width: 100%" />
            <span>
            <strong>VAT : 310414274700003</strong>
        </span>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="page-header">
                    <i class="fa fa-globe"></i>
                    رقم الطلب
                    <strong style="color: blue;display:inline-block">#{{$object->id}}</strong>



                    <small class="pull-left">تاريخ: {{@$object->getOrder->updated_at->format('Y-m-d h:i A')}}</small>
                </div>
            </div><!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <b>العميل</b><br>

                <address>
                    <strong> الاسم:{{@$object->user->username}}</strong><br>
                    جوال: {{'0'.@$object->user->phone}}<br>
                    @if(@$object->user->email)
                        البريد الألكتروني: {{$object->user->email}}<br>
                    @endif
                    {{--  <span>السجل الضريبي:{{@$object->user->commercial_no}}</span><br>
                      <span>الرقم الضريبي:{{@$object->user->tax_number}}</span><br>--}}
                </address>
            </div><!-- /.col -->

            <div class="col-sm-4 invoice-col">
                <b>العنوان</b><br>
                <address>
                    <span> الدولة:{{@$object->getOrder->country->name}}</span><br>
                    <span>المنطقة: {{@$object->getOrder->region->name}}</span><br>
                    <span>المدينة: {{@$object->getOrder->state->name}}</span><br>
                    <span>الشارع: {{@$object->getOrder->address_name}}</span><br>
                    <span> {{@$object->getOrder->address_desc}}</span><br>
                    {{--                {{@$object->state->name}}<br>--}}
                    {{--                {{@$object->address_name}}--}}
                    <br>
                </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>بيانات الطلب</b><br>
                <span> رقم الطلب:{{@$object->id}}</span><br>
                {{--            @if($object->status==0)--}}
                @if($object->payment_method==0)
                    <span> حالة الطلب: جاري المراجعة</span><br>
                    <span> طريقة الدفع:{{@$object->cartPaymentMethod!=null?@$object->cartPaymentMethod->name:@$object->cartPaymentMethod->name}}</span><br>
                @else
                    <span> طريقة الدفع:{{@$object->paymentMethod->name}}</span><br>
                @endif

                {{--            <span> تاريخ الطلب:{{@$object->id}}</span><br>--}}
            </div><!-- /.col -->
        </div><!-- /.row -->

        @if($object->cart_items)
            <div class="row">
                <label class="col-sm-12"><strong> تفاصيل الطلب</strong></label>
                <div class="col-sm-8">
                    <table class="table table-bordered">
                        <tr style="background: #e8e8e8">
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>سعر الوحدة</th>
                            <th>السعر الكلي</th>

                        </tr>
                        @php
                            $total_cart_price=0;
                        @endphp
                        @foreach($object->cart_items as $key=> $item)
                            @php
                                $total_cart_price+=$item->price*$item->quantity;
                            @endphp
                            <tr>
                                <td>{{@$item->product->title }}</td>
                                <td>{{ @$item->quantity }}  </td>
                                <td>{{ @$item->price }} ريال </td>
                                <td>{{ @$item->price*$item->quantity }} ريال </td>

                            </tr>
                        @endforeach

                        <tr style="background: #daefff">
                            <td colspan="3">اجمالي المنتجات</td>
                            <td>{{ round($total_cart_price,2)}} ريال </td>
                        </tr>
                    </table>

                </div>
                <div class="col-sm-8">
                    <table class="table table-bordered">
                        <tr style="background: #e8e8e8">
                            <td colspan="3">تكلفة المنتجات</td>
                            <td>{{ round($total_cart_price,2)}} ريال  </td>
                        </tr>
                        <tr style="background: #e8e8e8">
                            <td colspan="3">تكلفة الشحن</td>
                            <td>{{$object->getOrder->delivery_price}} ريال </td>
                        </tr>
                        @if($object->cobon_discount >0)
                            <tr style="background: #e8e8e8">
                                <td colspan="3">كوبون خصم (من الشحن )</td>
                                <td>- {{$object->cobon_discount}} % ({{$object->cobon_discount*$object->delivery_price/100}} ريال) </td>
                            </tr>
                        @endif
                        @if($object->taxes >0)

                            <tr style="background: #e8e8e8">
                                <td colspan="3">الضريبة</td>
                                <td> {{$object->taxes}} ريال  </td>
                            </tr>
                        @endif

                        <tr style="background: #daefff">
                            <td colspan="3">المجموع</td>
                            <td>{{ round($object->final_price,2)}} ريال </td>
                        </tr>
                    </table>
                </div>

            </div>
        @endif



        <div>
            <img src="/images/footer.PNG" style="width: 100%" />
        </div>
        <!-- this row will not appear when printing -->
    </section><!-- /.content -->
</div>
</body>
</html>
