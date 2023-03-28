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



                    <small class="pull-left">تاريخ: {{$object->updated_at->format('Y-m-d h:i A')}}</small>
                </div>
            </div><!-- /.col -->
        </div>
        <!-- info row -->
        <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
                <b>المورد</b><br>

                <address>
                    <strong> الاسم:{{@$object->provider->username}}</strong><br>
                    <strong>
                    جوال: {{'0'.@$object->provider->phone}}<br>
                    </strong>
                    @if(@$object->provider->email)
                        <strong>
                            البريد الألكتروني: {{$object->provider->email}}<br>
                        </strong>

                    @endif
                    {{--  <span>السجل الضريبي:{{@$object->user->commercial_no}}</span><br>
                      <span>الرقم الضريبي:{{@$object->user->tax_number}}</span><br>--}}
                </address>
            </div><!-- /.col -->


            <!-- /.col -->
            <div class="col-sm-4 invoice-col">
                <b>بيانات الطلب</b><br>
                <strong> رقم الطلب:{{@$object->id}}</strong><br>
                <strong>  تاريخ التسليم:{{@$object->delivery_date}}</strong><br>
                {{--            @if($object->status==0)--}}
                <span> حالة الطلب: {{@$object->orderStatus->name}}</span><br>
                <span> طريقة الدفع:{{@$object->paymentMethod->name}}</span><br>
                <span> شروط الدفع:{{@$object->paymentTerm->name}}</span><br>


                {{--            <span> تاريخ الطلب:{{@$object->id}}</span><br>--}}
            </div><!-- /.col -->

            <div class="col-sm-4 invoice-col">
                <div>
                    <span>يوم استلام الطلب:</span>
                    <strong>{{@$object->provider_delivery_date}}</strong>
                </div>
                <div>
                    <span>وقت استلام الطلب:</span>
                    <strong>{{@str_replace('TimeOfDay','',$object->provider_delivery_time)}}</strong>
                </div>
            </div>
        </div><!-- /.row -->

        @if($object->purchase_item)
            <div class="row">
                <label class="col-sm-12"><strong> تفاصيل الطلب</strong></label>
                <div class="col-sm-8">
                    <table class="table table-bordered">
                        <tr style="background: #e8e8e8">
                            <th>المنتج</th>
                            <th>الكمية المطلوبة</th>
                            <th>الكمية المستلمة</th>
                            <th>سعر الوحدة</th>
                            <th>السعر الكلي</th>

                        </tr>
                        @foreach($object->purchase_item as $key=> $item)
                            <tr>
                                <td>{{@$item->product->title }}</td>
                                <td>{{ $item->quantity }}  </td>
                                <td>{{ @$item->delivered_quantity }}  </td>
                                <td>{{ @$item->price }} ريال </td>
                                <td>{{ @$item->price*$item->quantity }} ريال </td>

                            </tr>
                        @endforeach

                        <tr style="background: #daefff">
                            <td colspan="4">اجمالي المنتجات</td>
                            <td>{{ round($object->order_price,2)}} ريال </td>
                        </tr>
                    </table>

                </div>
                <div class="col-sm-8">
                    <table class="table table-bordered">
                        <tr style="background: #e8e8e8">
                            <td colspan="4">تكلفة المنتجات</td>
                            <td>{{$object->order_price}} ريال </td>
                        </tr>

                        @if($object->taxes >0)

                            <tr style="background: #e8e8e8">
                                <td colspan="4">الضريبة</td>
                                <td> {{$object->taxes}} ريال  </td>
                            </tr>
                        @endif

                        <tr style="background: #daefff">
                            <td colspan="4">المجموع</td>
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
