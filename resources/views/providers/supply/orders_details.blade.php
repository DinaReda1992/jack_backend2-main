<table class="table table-bordered">
    <thead>
    <tr>
        <th>رقم الطلب</th>
        <th style="width: 200px">تاريخ الطلب</th>

        <th>شروط الدفع</th>

        <th>حالة الطلب</th>

        {{--        <th>تكلفة التوصيل</th>--}}
        <th>تفاصيل الطلب</th>

        <th>الاجراء المتخذ</th>
    </tr>
    </thead>
    <tbody>

    @foreach($objects as $object)
        <tr parent_id="{{ $object->id }}">

            <td>{{ $object->id }}</td>
            <td>{{ $object->created_at->diffForHumans() }}</td>

            <td>
                {{ @$object->paymentTerm->name }}
            </td>

            <td>
                {{ @$object->orderStatus->name }}
            </td>

            <td>
                <a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
                   href="/project-details/{{ $object -> id }}"> تفاصيل الطلب ({{ @$object->purchase_item->count() }}
                    منتجات)
                </a>

                <div class="modal fade bd-example-modal-lg" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content" style="min-width: 987px;">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
                                <a href="/provider-panel/supply/{{$object->id}}/edit" target="_blank"
                                   class="btn btn-default pull-right"><i class="fa fa-print"></i> Print</a>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div>
                                            <span>التاريخ المطلوب لاستلام الطلب:</span>
                                            <span>{{$object->delivery_date}}</span>
                                        </div>
                                        <div>
                                            <span>حالة الطلب:</span>
                                            <span class="badge {{$object->status==4?'badge-danger':'badge-info'}}">{{$object->orderStatus->name}}</span>
                                        </div>
                                        <div>
                                            <span>شروط الدفع:</span>
                                            <span>{{@$object->paymentTerm->name}}</span>
                                        </div>

                                        @if($object->transfer_photo!=null)
                                            @php
                                                $allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
                                                    $contentType = @mime_content_type('uploads/'.@$object->transfer_photo);


                                            @endphp
                                            <div>
                                                <p class="d-block">ايصال التحويل:</p>
                                                <span>
                                                    @if(in_array($contentType, $allowedMimeTypes) )
                                                        <a data-fancybox
                                                           data-caption="صورة التحويل">
                                                    <img width="300" src="/uploads/{{@$object->transfer_photo}}" /></a>
                                                    @else
                                                        <a target="_blank" href="/uploads/{{@$object->transfer_photo}}">شاهد الملف</a>
                                                    @endif
                                                </span>
                                            </div>


                                        @endif
                                    </div>
                                    @if($object->status>2)
                                    <div class="col-md-6">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div>
                                                    <span>يوم استلام الطلب:</span>
                                                    <span>{{@$object->provider_delivery_date}}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <span>وقت استلام الطلب:</span>
                                                    <span>{{@str_replace('TimeOfDay','',$object->provider_delivery_time)}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        @endif
                                </div>
                                @if($object->purchase_item->count()>0)
                                    <div class="row">
                                        <label class="col-sm-12"><strong> تفاصيل الطلب</strong></label>
                                        <div class="col-sm-12">
                                            <table class="table table-bordered">
                                                <tr style="background: #e8e8e8">
                                                    <th>المنتج</th>
                                                    <th>سعر الوحدة</th>
                                                    <th>الكمية المطلوبة</th>
                                                    <th>الكمية المستلمة</th>
                                                    <th style="min-width: 103px;">السعر</th>
                                                </tr>
                                                @foreach($object->purchase_item as $detail)
                                                    <tr style="color:{{$detail->status==5?'red':''}}">
                                                        <td>{{ @$detail->product->title }}  </td>
                                                        <td>{{ $detail->price }} ريال</td>
                                                        <td>{{ $detail->quantity }}  </td>
                                                        <td>{{ @$detail->delivered_quantity }}  </td>
                                                        <td>{{ $detail->price*$detail->quantity }} ريال</td>
                                                    </tr>
                                                @endforeach


                                                <tr style="background: #daefff">
                                                    <td colspan="4">الضريبة</td>
                                                    <td>{{ @$object->taxes}}
                                                        ريال
                                                    </td>
                                                </tr>
                                                <tr style="background: #daefff">
                                                    <td colspan="4">المجموع</td>
                                                    <td>{{ @$object->final_price}}
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
                        @if($object->status==7)
                            <span style="color:green">مكتمل </span>
                        @elseif($object->status<=2)
                            <a onclick="return false;" data-toggle="modal"
                               data-target="#myModal{{ $object->id }}555" href="#">
                                <button type="button" name="button"
                                        class="btn btn-primary"> {{$object->orderStatus->btn_text}}</button>
                            </a>
                            <div class="modal fade " id="myModal{{ $object->id }}555" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel">
                                <div class="modal-dialog " role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close"><span aria-hidden="true">&times;</span>
                                            </button>
                                            <h4 class="modal-title" id="myModalLabel">رسالة تغيير حالة الطلب</h4>
                                        </div>
                                        <form method="post"
                                              action="/provider-panel/supply/change_order_status/{{ $object ->id }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="row">
                                                    @if($object->status==2)
                                                    <div class="form-group col-md-6">
                                                        <label for="inputCity">يوم التسليم</label>
                                                        <input name="id" type="hidden" value="{{old('id',$object->id)}}" />
                                                        <input name="provider_delivery_date" type="date" class="form-control" />
                                                    </div>

                                                    <div class="form-group col-md-6">
                                                        <label for="inputZip">موعد التسليم</label>
                                                        <input name="provider_delivery_time" type="time" class="form-control" />
                                                    </div>
                                                        @endif
                                                </div>

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

                @endif


            </td>
        </tr>


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
