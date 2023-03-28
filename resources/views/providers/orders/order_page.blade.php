@extends('providers.layout')
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
                {{--							<div class="page-title">--}}
                {{--								<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} عضو</h4>--}}
                {{--							</div>--}}

                <!-- 						<div class="heading-elements"> -->
                    <!-- 							<div class="heading-btn-group"> -->
                    <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a> -->
                    <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a> -->
                    <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a> -->
                    <!-- 							</div> -->
                    <!-- 						</div> -->
                </div>

                <div class="breadcrumb-line">
                    <ul class="breadcrumb">
                        <li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                        <li><a href="/provider-panel/orders">عرض الطلبات</a></li>
                        <li class="active">تفاصيل الطلب</li>
                    </ul>


                    <!-- 						<ul class="breadcrumb-elements"> -->
                    <!-- 							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li> -->
                    <!-- 							<li class="dropdown"> -->
                    <!-- 								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
                    <!-- 									<i class="icon-gear position-left"></i> -->
                    <!-- 									Settings -->
                    <!-- 									<span class="caret"></span> -->
                    <!-- 								</a> -->

                    <!-- 								<ul class="dropdown-menu dropdown-menu-right"> -->
                    <!-- 									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li> -->
                    <!-- 									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li> -->
                    <!-- 									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li> -->
                    <!-- 									<li class="divider"></li> -->
                    <!-- 									<li><a href="#"><i class="icon-gear"></i> All settings</a></li> -->
                    <!-- 								</ul> -->
                    <!-- 							</li> -->
                    <!-- 						</ul> -->
                </div>
            </div>


        </section>
    @include('providers.message')

    {{--				<div class="pad margin no-print">--}}
    {{--					<div class="callout callout-info" style="margin-bottom: 0!important;">--}}
    {{--						<h4><i class="fa fa-info"></i> Note:</h4>--}}
    {{--						This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.--}}
    {{--					</div>--}}
    {{--				</div>--}}

    <!-- Main content -->
        <section class="invoice">
            <!-- title row -->
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        <i class="fa fa-globe"></i>
                        رقم الطلب
                        <span style="color: blue">{{$object->id}}#  </span>
                        <span style="color: {{@$object->orderStatus->color}};font-size: 15px;">
																(	{{ @$object->orderStatus->name  }}  )

								</span>


                    </h2>
                </div><!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                    العميل
                    <address>
                        <strong>{{$object->user->username}}</strong><br>


                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    العنوان
                    <address>
                        <strong>{{$object->getOrder->address->state->name}}</strong><br>
{{--                        <a target="_blank"--}}
{{--                           href="http://maps.google.com/maps?q={{ @$object->latitude }},{{ @$object->longitude }}">--}}
{{--                            {{$object->address}}--}}
{{--                        </a>--}}
                        <br>
                    </address>
                </div><!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    <b>بيانات الطلب</b><br>
                    <br>
                    <b>رقم الطلب:</b> {{$object->id}}<br>

                    <b>نوع الدفع:</b>                 {{ @$object->getOrder->paymentMethod->name }}

                </div><!-- /.col -->
            </div><!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-xs-12 table-responsive">
                    <p class="lead" style="text-align: right">تفاصيل الطلب</p>

                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>السعر</th>
                            <th>الحالة</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach($object->cart_items as $detail)
                            <tr style="color:{{$detail->status==5?'red':''}}">

                                <td>@if($detail->status==5)
                                        <s>
                                            @endif
                                            {{$detail->type==1? @$detail->itemProduct->title:$detail->itemProduct->part->part_name }}
                                            @if($detail->status==5)
                                        </s>
                                    @endif
                                </td>
                                <td>{{ $detail->type==1?'من المتجر':'عرض تسعير' }}</td>
                                <td>{{ $detail->quantity }}  </td>

                                <td>{{ $detail->price }} ريال </td>
                                <td>{{$detail->status==5?'ملغى':$object->orderStatus->name}} </td>


                            </tr>
                        @endforeach
                    </table>
                </div><!-- /.col -->
            </div><!-- /.row -->

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-6">
                    <p class="lead">الاجراء المتخذ:</p>
                    <div class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                        @if($object->status==5)
                            <span style="color:red">تم الغاء الطلب</span>
                        @else
                        <td style="text-align: center">
                            @if($shipment->id==4)
                                @if($object->status==4)
                                    <span style="color:green">مكتمل </span>
                                @else
                                    <a onclick="return false;" data-toggle="modal"
                                       data-target="#myModal{{ $object->id }}555" href="#">
                                        <button type="button" name="button" class="btn btn-primary"> {{$object->orderStatus->btn_text}}</button>
                                    </a>
                                    <div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1" role="dialog"
                                         aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close"><span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <h4 class="modal-title" id="myModalLabel">رسالة تغيير حالة الطلب</h4>
                                                </div>
                                                <form method="get" action="/provider-panel/change_order_status/{{ $object ->id }}">
                                                    <div class="modal-body">

                                                        <div class="form-group">
                                                            <label for="exampleInputEmail1"></label>
                                                            هل انت متأكد من تغيير حالة هذا الطلب الى ({{$object->orderStatus->btn_text}})
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <a type="button" class="btn btn-default"
                                                           data-dismiss="modal">اغلاق</a>
                                                        <button type="submit" class="btn btn-primary">ارسال</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                            @else
                                @if($object->status==1)
                                    <a  href="/provider-panel/send-order-shipment/{{$object->id}}">
                                        <button type="button" name="button" class="btn btn-primary"> ارسال الطلب للشحن</button>
                                    </a>
                                @elseif($object->status==4)
                                    <span style="color:green">مكتمل </span>
                                    @if($object->shipment_company==1)
                                        <a href="http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}" target="popup" onclick="window.open('http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}','name','width=700,height=700')"
                                           style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i class="fa fa-print"></i></a>
                                    @endif
                                    @if($object->shipment_company==2)
                                        <a href="{{$object->shipment_attach}}" target="popup" onclick="window.open('{{$object->shipment_attach}}','name','width=700,height=700')"
                                           style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i class="fa fa-print"></i></a>
                                    @endif
                                @else
                                    <span style="color:orange">تم الإرسال لشركة الشحن </span>
                                    @if($object->shipment_company==1)
                                        <a href="http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}" target="popup" onclick="window.open('http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}','name','width=700,height=700')"
                                           style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i class="fa fa-print"></i></a>
                                    @elseif($object->shipment_company==2)
                                        <a href="{{$object->shipment_attach}}" target="popup" onclick="window.open('{{$object->shipment_attach}}','name','width=700,height=700')"
                                           style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i class="fa fa-print"></i></a>
                                    @endif
                                @endif
                            @endif



                        </td>
@endif
                    </div>
                </div><!-- /.col -->
                <div class="col-xs-6">
                    <p class="lead">الملخص</p>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>

                            <tr style="background: #e8e8e8">
                                <td colspan="3">تكلفة الشحن</td>
                                <td>{{$object->delivery_price}} ريال </td>
                            </tr>
                            <tr style="background: #e8e8e8">
                                <td colspan="3">الضريبة</td>
                                <td> {{$object->taxes}} ريال  </td>
                            </tr>

                            <tr style="background: #daefff">
                                <td colspan="3">المجموع</td>
                                <td>{{ @$object->items_price + $object->delivery_price+$object->taxes}} ريال </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.col -->
            </div><!-- /.row -->

            <!-- this row will not appear when printing -->
            <div class="row no-print">
                <div class="col-xs-12">

                    <a href="/provider-panel/invoice-print/{{$object->id}}" target="_blank"
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
