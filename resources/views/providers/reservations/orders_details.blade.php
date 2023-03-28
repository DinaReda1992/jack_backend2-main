<table class="table table-bordered">
    <thead>
    <tr>
        <th>رقم الطلب</th>
        <th style="width: 200px">تاريخ الطلب</th>
        {{--        @if($type == 1 || $type== 2)--}}
        {{--        <th style="width: 200px">تاريخ قبول الطلب</th>--}}
        {{--        @endif--}}
        {{--        @if($type ==  2)--}}
        {{--            <th style="width: 200px">تاريخ تسليم الطلب</th>--}}

        {{--        @endif--}}
        <th>صاحب الطلب</th>
        @if($type == 1 || $type == 2 )
            <th>المندوب</th>
        @endif
        <th>المتجر المطلوب التوصيل منه</th>
        <th>توصيل من</th>
        <th>توصيل الى</th>
        {{--        <th>تكلفة التوصيل</th>--}}
        @if($type == 1 || $type == 2 )
            <th>رسائل التواصل</th>
        @endif
        <th>العروض على الطلب</th>
        <th>تفاصيل الطلب</th>
        {{--<th>الاجراء المتخذ</th>--}}
    </tr>
    </thead>
    <tbody>
    @php
        $i=1;
    @endphp
    @foreach($objects as $object)
        <tr parent_id="{{ $object->id }}">

            <td>{{ $object->id }}</td>
            <td>{{ $object->created_at->format('Y/m/d h:i A') }}</td>
            {{--            @if($type == 1 || $type== 2)--}}
            {{--                <td>{{ $object->accept_date }}</td>--}}
            {{--            @endif--}}
            {{--            @if($type == 2 )--}}
            {{--                <td>{{ $object->deliver_date }}</td>--}}
            {{--            @endif--}}
            <td><a href="/provider-panel/all-users/{{ $object->user_id }}/edit">{{ @$object->getUser->username }}</a></td>
            @if($type == 1 || $type== 2)
                <td>
                    <a href="/provider-panel/all-users/{{ $object->representative_id }}/edit">{{ @$object->getRepresentative ?  @$object->getRepresentative->username : '___________' }}</a>
                </td>
            @endif
            <td>{{ $object->store_name }}</td>

            <td>
                {{ @$object->from_address }}
            </td>
            <td>
                {{ @$object->to_address }}
            </td>
            {{--            <td>{{ @$object->price_after_discount ? $object->price_after_discount : $object->final_price   }}   ريال </td>--}}
            {{--<td>--}}
            {{--<a onclick="return false;" data-toggle="modal"--}}
            {{--data-target="#myModal{{ $object->id }}555" href="#">--}}
            {{--<button type="button" name="button" class="btn btn-danger"> الغاء الطلب</button>--}}
            {{--</a>--}}

            {{--<div class="modal fade" id="myModal{{ $object->id }}555" tabindex="-1" role="dialog"--}}
            {{--aria-labelledby="myModalLabel">--}}
            {{--<div class="modal-dialog" role="document">--}}
            {{--<div class="modal-content">--}}
            {{--<div class="modal-header">--}}
            {{--<button type="button" class="close" data-dismiss="modal"--}}
            {{--aria-label="Close"><span aria-hidden="true">&times;</span>--}}
            {{--</button>--}}
            {{--<h4 class="modal-title" id="myModalLabel">رسالة إلغاء الطلب</h4>--}}
            {{--</div>--}}
            {{--<form method="get" action="/provider-panel/cancel_order/{{ $object ->id }}">--}}
            {{--<div class="modal-body">--}}

            {{--<div class="form-group">--}}
            {{--<label for="exampleInputEmail1"></label>--}}
            {{--<textarea name="reason_of_cancel"--}}
            {{--class="form-control"></textarea>--}}
            {{--</div>--}}

            {{--</div>--}}
            {{--<div class="modal-footer">--}}
            {{--<a type="button" class="btn btn-default"--}}
            {{--data-dismiss="modal">اغلاق</a>--}}
            {{--<button type="submit" class="btn btn-primary">ارسال</button>--}}
            {{--</div>--}}
            {{--</form>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}

            {{--</td>--}}
            @if($type == 1 || $type== 2)
                <td><a href="/provider-panel/order-messages/{{ $object->id }}">عرض الرسائل
                        ( {{ \App\Models\Messages::where('order_id',$object->id)->count() }} )</a></td>
            @endif
            <td>
                <a data-toggle="modal" data-target="#offers{{ $object->id }}" onclick="return false;"
                   href="/project-offers/{{ $object -> id }}"> {{ $object->offers->count() }} عرض
                </a>

                <div class="modal fade" id="offers{{ $object->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">العرض المقدمة على الطلب</h4>
                            </div>
                            <div class="modal-body">
                                <hr>
                                @foreach($object->offers as $offer)
                                    <div class="row">
                                        <label class="col-sm-4"><strong> صاحب العرض</strong></label>
                                        <div class="col-sm-8">{{ @$offer->getUser->username }}</div>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-4"><strong> سعر التوصيل </strong></label>
                                        <div class="col-sm-8">{{ @$offer->price }} ريال سعودي</div>
                                    </div>
                                    <hr>
                                @endforeach
                                @if($object->offers->count()==0)
                                    <div class="row">
                                        <div class="col-sm-12">

                                            <h5 align="center">لا يوجد عروض بعد</h5>
                                        </div>
                                    </div>

                                @endif

                            </div>
                            {{--http://www.google.com/maps/place/lat,lng--}}
                            {{--http://maps.google.com/maps?q=24.197611,120.780512--}}
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </td>
            <td>
                <a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
                   href="/project-details/{{ $object -> id }}"> تفاصيل الطلب
                </a>

                <div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">تفاصيل الطلب</h4>
                            </div>
                            <div class="modal-body">
                                <hr>
                                <div class="row">
                                    <label class="col-sm-4"><strong> صورة المتجر</strong></label>
                                    <div class="col-sm-8"><img style="width: 200px;height: 150px"
                                                               src="{{ $object->store_photo }}"></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <label class="col-sm-4"><strong> اسم المتجر </strong></label>
                                    <div class="col-sm-8">{{ @$object->store_name }}</div>
                                </div>
                                <hr>

                                <div class="row">
                                    <label class="col-sm-4"><strong> صاحب الطلب</strong></label>
                                    <div class="col-sm-8">{{ @$object->getUser->username }}</div>
                                </div>
                                <hr>
                                <div class="row">
                                    <label class="col-sm-4"><strong> توصيل من</strong></label>
                                    <div class="col-sm-8">
                                        <a target="_blank"
                                           href="http://maps.google.com/maps?q={{ $object->from_lat }},{{ $object->from_long }}">
                                            {{ @$object->from_address }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <label class="col-sm-4"><strong> توصيل الى</strong></label>
                                    <div class="col-sm-8">
                                        <a target="_blank"
                                           href="http://maps.google.com/maps?q={{ $object->to_lat }},{{ $object->to_long }}">
                                            {{ @$object->to_address }}
                                        </a>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <label class="col-sm-4"><strong> وقت التوصيل</strong></label>
                                    <div class="col-sm-8">
                                        {{ @$object->getDeliveryTime->name }}
                                    </div>
                                </div>
                                <hr>
                                @if($object->description)
                                    <div class="row">
                                        <label class="col-sm-4"><strong> وصف الطلب</strong></label>
                                        <div class="col-sm-8">{{ @$object->description }}</div>
                                    </div>
                                    <hr>
                                @endif
                                @if($object->photos->count()>0)
                                    <div class="row">
                                        <label class="col-sm-4"><strong> صور الطلب</strong></label>
                                        <div class="col-sm-8">
                                            @foreach($object->photos as $photo)
                                                <div align="center"
                                                     style="height: 75px;width: 100px;float: right;margin-right: 5px">
                                                    <img alt="" width="100" height="75"
                                                         src="/uploads/{{ $photo->photo }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <hr>
                                @endif
                                @if($object->representative_id)
                                    <div class="row">
                                        <label class="col-sm-4"><strong> سعر التوصيل</strong></label>
                                        <div class="col-sm-8">
                                            <table class="table">
                                                <tr>
                                                    <th>
                                                        صاحب العرض الموافق عليه
                                                    </th>
                                                    <td>
                                                        {{ @$object->getOffer->getUser->username }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        السعر
                                                    </th>
                                                    <td>
                                                        {{ @$object->getOffer->price }} ريال سعودي
                                                    </td>
                                                </tr>


                                                @if($object->cobon)
                                                    <tr>
                                                        <th>
                                                            كود الخصم
                                                        </th>
                                                        <td>
                                                            <span style="direction: ltr;float: left;">{{ @$object->cobon }} -  {{ @$object->getCobon->percent }} %</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            السعر الاجمالي بعد الخصم
                                                        </th>
                                                        <td>
                                                            {{ $object->price_after_discount }}  ريال
                                                        </td>
                                                    </tr>

                                                @endif

                                            </table>
                                        </div>
                                    </div>
                                    <hr>
                                @endif
                            </div>
                            {{--http://www.google.com/maps/place/lat,lng--}}
                            {{--http://maps.google.com/maps?q=24.197611,120.780512--}}
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </td>

        </tr>

        @php
            $i++;
        @endphp
    @endforeach
    @if($type==0 || $type==3 )
        @if(count($objects)==0)
            <tr>
                <td colspan="8" align="center">لا يوجد طلبات بعد</td>
            </tr>
        @endif
    @endif
    @if($type==1 ||$type==2  )
        @if(count($objects)==0)
            <tr>
                <td colspan="11" align="center">لا يوجد طلبات بعد</td>
            </tr>
        @endif
    @endif

    </tbody>
</table>

<div class="clearfix"></div>
<br>
<hr>
<div align="center">
    {!! $objects->render() !!}
</div>