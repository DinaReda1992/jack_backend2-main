<div class="table-responsive">

<table class="table table-bordered">
    <thead>
    <tr>
        <th>رقم الطلب</th>
        <th style="width: 200px">تاريخ انشاء الطلب</th>
        <th style="width: 200px">وقت انشاء الطلب</th>
        <th>اسم العميل</th>


        <th style="width: 127px">تفاصيل الطلب</th>

        <th> الاجراء المتخذ</th>
    </tr>
    </thead>
    <tbody>

    @foreach($objects as $object)
        <tr parent_id="{{ $object->id }}">

            <td>{{ $object->id }}</td>
            <td>{{ $object->created_at->format('Y-m-d') }}</td>
            <td>{{ $object->created_at->diffForHumans() }}</td>

            <td>
                <a href="{{'/admin-panel/all-users/'.@$object->user->id.'/edit'}}">{{ @$object->user->username }}</a>
            </td>


           <td>
               <a href="{{url('admin-panel/delivery-orders/'.$object->id)}}"
               > التفاصيل ( {{ @$object->cart_items->count() }} )
               </a>

            </td>
                <td style="text-align: center">
                    @if($object->status==5)
                        <span style="color:red">تم الغاء الطلب</span>

                    @else
                        @if($object->status==7)
                            <span style="color:green">مكتمل </span>
                        @elseif($object->status<3)
                            <span class="badge badge-info">{{$object->orderStatus->name}} </span>
                        @elseif($object->status>=3)
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
                                              action="/admin-panel/delivery-orders/change_order_status/{{ $object ->id }}">
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

                    @endif


                </td>

        </tr>

    @endforeach
        @if(count($objects)==0)
            <tr>
                <td colspan="11" align="center">لا يوجد طلبات بعد</td>
            </tr>
        @endif

    </tbody>
</table>

<div class="clearfix"></div>
<br>
<hr>
<div align="center">
    {!! $objects->render() !!}
</div>
</div>
