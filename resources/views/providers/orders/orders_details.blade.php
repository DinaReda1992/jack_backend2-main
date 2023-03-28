<table class="table table-bordered">
    <thead>
    <tr>
        <th>رقم الطلب</th>
        <th style="width: 200px">تاريخ الطلب</th>

        <th>صاحب الطلب</th>
        <th>نوع الدفع</th>

        <th>حالة الطلب</th>

        {{--        <th>تكلفة التوصيل</th>--}}
        <th>تفاصيل الطلب</th>

        <th>الاجراء المتخذ</th>
    </tr>
    </thead>
    <tbody>
    @php
        $i=1;
    @endphp
    @foreach($objects as $object)
        <tr parent_id="{{ $object->id }}">

            <td>{{ $object->id }}</td>
            <td>{{ $object->created_at->diffForHumans() }}</td>
            <td>
                <a>{{ @$object->user->username }}</a></td>
            <td>
                {{ @$object->getOrder->paymentMethod->name }}
            </td>

            <td>
                <a href="/provider-panel/order-details/{{$object->id}}"
                   style="color:{{$object->status==5?'red':''}}">{{ @$object->orderStatus->name }}</a></td>

            {{--                        <td>{{ @$object->price_after_discount ? $object->price_after_discount : $object->final_price   }}   ريال </td>--}}


            <td>
                <a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
                   href="/project-details/{{ $object -> id }}"> تفاصيل الطلب ({{ @$object->cart_items->count() }}
                    منتجات)
                </a>

                <div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="min-width: 987px;">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
                                <a href="/provider-panel/invoice-print/{{$object->id}}" target="_blank"
                                   class="btn btn-default pull-right"><i class="fa fa-print"></i> Print</a>
                            </div>
                            <div class="modal-body">


                                <div class="row">
                                    <label class="col-sm-4"><strong>صاحب الطلب</strong></label>

                                    <div class="col-sm-12">
                                        <div class="box-body box-profile">
                                            {{--                                            <img class="profile-user-img img-responsive img-circle" src="/uploads/{{@$object->user->photo?:'default-user.png' }}" style="height: 100px;" alt="User profile picture">--}}
                                            <h3 class="profile-username text-center">{{ @$object->user->username }}</h3>
                                            <p class="text-muted text-center">{{@$object->getOrder->address->state->name}}</p>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                {{--                                <div class="row">--}}
                                {{--                                    <label class="col-sm-4"><strong> سعر الطلب</strong></label>--}}
                                {{--                                    <div class="col-sm-8">--}}
                                {{--                                        {{ @$object->shipment->cart_items->sum('price') }} ريال--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}

                                <hr>
                                @if($object->cart_items->count()>0)
                                    <div class="row">
                                        <label class="col-sm-12"><strong> تفاصيل الطلب</strong></label>
                                        <div class="col-sm-12">
                                            <table class="table table-bordered">
                                                <tr style="background: #e8e8e8">
                                                    <th>المنتج</th>
                                                    <th>موديل السيارة</th>
                                                    <th>رقم القطعة</th>

                                                    <th>النوع</th>
                                                    <th>الكمية</th>
                                                    <th style="min-width: 103px;">السعر</th>

                                                </tr>
                                                @foreach($object->cart_items as $detail)
                                                    <tr style="color:{{$detail->status==5?'red':''}}">

                                                        <td>
                                                            @if($detail->status==5)
                                                                <s>
                                                                    @endif
                                                                    {{@$detail->type==1? @$detail->itemProduct->title:@$detail->itemProduct->part->part_name }}
                                                                    @if($detail->status==5)
                                                                </s>
                                                            @endif
                                                        </td>
{{--                                                        <td>{{ $detail->type==1?@$detail->itemProduct->model->name.' ('.@$detail->itemProduct->makeYearsText().')':'-' }}</td>--}}
                                                        <td>{{ $detail->type==1?@$detail->itemProduct->part_no:'-' }}</td>

                                                        <td>
                                                            @if($detail->type==1)
                                                                <span>من المتجر</span>
                                                            @else
                                                                <a href="/provider-panel/pricing-part-order/{{@\App\Models\PricingOffer::find($detail->item_id)->part_id}}">عرض تسعير</a>
                                                                @endif
                                                        </td>
                                                        <td>{{ $detail->quantity }}  </td>

                                                        <td>{{ $detail->price }} ريال</td>


                                                    </tr>
                                                @endforeach
                                                <tr style="background: #e8e8e8">
                                                    <td colspan="5">تكلفة الشحن</td>
                                                    <td>{{$object->delivery_price}} ريال</td>
                                                </tr>
                                                <tr style="background: #e8e8e8">
                                                    <td colspan="5">( {{@$object->getOrder->cobon_discount.' %'}}
                                                        )الخصم
                                                    </td>
                                                    <td> {{$object->items_price*@$object->getOrder->cobon_discount/100}}
                                                        ريال
                                                    </td>
                                                </tr>

                                                <tr style="background: #daefff">
                                                    <td colspan="5">المجموع</td>
                                                    <td>{{ @$object->items_price + $object->delivery_price+$object->taxes}}
                                                        ريال
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <hr>
                                @endif

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </td>
            <td style="text-align: center">
                @if($object->status==5)
                    <span style="color:red">تم الغاء الطلب</span>

                @else
                    @if($shipment->id==4)
                        @if($object->status==4)
                            <span style="color:green">مكتمل </span>
                        @else
                            <a onclick="return false;" data-toggle="modal"
                               data-target="#myModal{{ $object->id }}555" href="#">
                                <button type="button" name="button"
                                        class="btn btn-primary"> {{$object->orderStatus->btn_text}}</button>
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
                                        <form method="get"
                                              action="/provider-panel/change_order_status/{{ $object ->id }}">
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <label for="exampleInputEmail1"></label>
                                                    هل انت متأكد من تغيير حالة هذا الطلب الى
                                                    ({{$object->orderStatus->btn_text}})
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
                            <a href="/provider-panel/send-order-shipment/{{$object->id}}">
                                <button type="button" name="button" class="btn btn-primary"> ارسال الطلب للشحن</button>
                            </a>
                        @elseif($object->status==4)
                            <span style="color:green">مكتمل </span>
                            @if($object->shipment_company==1)
                                <a href="http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}"
                                   target="popup"
                                   onclick="window.open('http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}','name','width=700,height=700')"
                                   style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i
                                            class="fa fa-print"></i></a>
                            @endif
                            @if($object->shipment_company==2)
                                <a href="{{$object->shipment_attach}}" target="popup"
                                   onclick="window.open('{{$object->shipment_attach}}','name','width=700,height=700')"
                                   style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i
                                            class="fa fa-print"></i></a>
                            @endif
                        @else
                            <span style="color:orange">تم الإرسال لشركة الشحن </span>
                            @if($object->shipment_company==1)
                                <a href="http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}"
                                   target="popup"
                                   onclick="window.open('http://track.smsaexpress.com/getPDF2.aspx?awbNo={{$object->shipment_no}}','name','width=700,height=700')"
                                   style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i
                                            class="fa fa-print"></i></a>
                            @elseif($object->shipment_company==2)
                                <a href="{{$object->shipment_attach}}" target="popup"
                                   onclick="window.open('{{$object->shipment_attach}}','name','width=700,height=700')"
                                   style="margin-right: 7px;background: #3c668f;color: #fff;" class="btn">Print <i
                                            class="fa fa-print"></i></a>
                            @endif
                        @endif
                    @endif
                @endif


            </td>
        </tr>

        @php
            $i++;
        @endphp
    @endforeach


    </tbody>
</table>

<div class="clearfix"></div>
<br>
<div class="datatable-footer" style="    padding: 10px 30px 0px 30px;">
    @php
    $from_page=($objects->count()*$objects->currentPage())-$objects->count()+1;
$from_page=$objects->currentPage()==$objects->lastPage()?$objects->lastItem()-$objects->Count()+1:$from_page;

$to_page=($objects->currentPage()*$objects->perPage());
$to_page=$to_page>$objects->total()?$objects->total():$to_page;
    @endphp
        <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">يظهر {{$from_page}} الى
        {{$to_page}} من {{$objects->total()}} اجمالي
        الصفوف
    </div>
    <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
        {{ $objects->appends(Request::except('page'))->links() }}
    </div>
</div>
