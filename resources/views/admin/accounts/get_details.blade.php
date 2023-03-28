@extends('admin.layout')
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
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -    عرض حسابات المندوب {{ $user->username }} </h4>
                </div>

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
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li class="active"><a href="">عرض حسابات المندوب {{ $user->username }}  </a></li>
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
        <!-- /page header -->


        <!-- Content area -->
        <div class="content">
        @include('admin.message')
        <!-- Basic example -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">عرض حسابات المندوب {{ $user->username }}  </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <table class="table datatable-colvis-basic">
                    <thead>
                    <tr>

                        <th>الرقم التعريفي</th>
                        <th>وقت العملية</th>
                        <th> المندوب</th>
                        <th>رصيده من العملية</th>
                        <th>نوع العملية</th>
                        <th>صاحب الطلب</th>
                        <th>تكلفة التوصيل</th>
                        {{--<th>حالة الارباح</th>--}}
                        <th>ارباح التطبيق</th>
                        <th>عرض الطلب</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $i=1 @endphp
                    @foreach($objects as $object)
                        <tr parent_id="{{ $object->id }}">
                            <td>{{ $i }}</td>
                            <td>{{ $object->created_at->diffForHumans() }}</td>
                            <td>{{ $object->getUser->username }}</td>
                            <td>
                                @php
                                    if($object->price < 0){
                                         echo "<span style='color:red;direction:ltr'>".$object->price." ريال</span>";
                                     }else{
                                         echo "<span style='color:green;direction:ltr'>".$object->price." ريال</span>";
                                     }
                                @endphp
                            </td>
                            <td>{{ $object->getType->name }}</td>
                            <td>{{ $object->getOrder ? $object->getOrder->getUser->username : "____" }}</td>
                            <td>
                                @if($object->getOrder)
                                    {{ $object->getOrder->price_after_discount ? $object->getOrder->price_after_discount : $object->getOrder->final_price }} ريال
                                @else
                                    _______
                                @endif
                            </td>
                            {{--<td>{{ $object->payment==0 ? 'لم يتم تحصيلها' : 'تم تحصيلها' }}</td>--}}
                            <td>
                                @if($object->site_profits)
                                {{ $object->site_profits }} ريال </td>
                                @else
                                _______
                                @endif
                            <td>
                                @if($object->getOrder)
                                <a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;"
                                   href="/project-details/{{ $object -> id }}"> عرض
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
                                                        <label class="col-sm-4"><strong> نوع الطلب</strong></label>
                                                        <div class="col-sm-8">{{ @$object->getOrder->getService->name }}</div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> صاحب الطلب</strong></label>
                                                        <div class="col-sm-8">{{ @$object->getOrder->getUser->username }}</div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> توصيل من</strong></label>
                                                        <div class="col-sm-8">
                                                            @if($object->getOrder->service_id!=4 && $object->getOrder->service_id!=5)
                                                                <a target="_blank" href="http://maps.google.com/maps?q={{ $object->getOrder->from_lat }},{{ $object->getOrder->from_long }}">
                                                                    {{ @$object->getOrder->from_name }} ( {{ @$object->getOrder->from_address }} )
                                                                </a>
                                                            @elseif($object->getOrder->service_id==4)
                                                                {{ @$object->getOrder->getFlight->fromCity->name }}
                                                            @elseif($object->getOrder->service_id==5)
                                                                {{ @$object->getOrder->getCarTrip->fromCity->name }}
                                                            @endif

                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> توصيل الى</strong></label>
                                                        <div class="col-sm-8">
                                                            @if($object->getOrder->service_id!=4 && $object->getOrder->service_id!=5)

                                                                <a target="_blank" href="http://maps.google.com/maps?q={{ $object->getOrder->to_lat }},{{ $object->getOrder->to_long }}">{{ @$object->getOrder->to_name }} ( {{ @$object->getOrder->to_address }} ) </a>

                                                            @elseif($object->getOrder->service_id==4)
                                                                {{ @$object->getOrder->getFlight->toCity->name }}
                                                            @elseif($object->getOrder->service_id==5)
                                                                {{ @$object->getOrder->getCarTrip->toCity->name }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    @if($object->getOrder->description)
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> وصف الطلب</strong></label>
                                                            <div class="col-sm-8">{{ @$object->getOrder->description }}</div>
                                                        </div>
                                                        <hr>
                                                    @endif
                                                    @if($object->getOrder->shipments)
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> عدد الشحنات</strong></label>
                                                            <div class="col-sm-8">{{ @$object->getOrder->shipments }}</div>
                                                        </div>
                                                        <hr>
                                                    @endif

                                                    @if($object->getOrder->photo)
                                                        <div class="row">
                                                            <label class="col-sm-4"><strong> صورة مرفقة</strong></label>
                                                            <div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->getOrder->photo }}"></div>
                                                        </div>
                                                        <hr>
                                                    @endif
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> سعر التوصيل</strong></label>
                                                        <div class="col-sm-8">
                                                            <table class="table">
                                                                @if($object->getOrder->service_id!=4 && $object->getOrder->service_id!=5 )
                                                                    <tr>
                                                                        <th>
                                                                            سعر التوصيل للكيلو
                                                                        </th>
                                                                        <td>
                                                                            {{ $object->getOrder->price_for_km }} ريال
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>
                                                                            مسافة التوصيل
                                                                        </th>
                                                                        <td>
                                                                            {{ $object->getOrder->distance }} KM

                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                                <tr>
                                                                    <th>
                                                                        السعر الاجمالي
                                                                    </th>
                                                                    <td>
                                                                        {{ $object->getOrder->final_price }} ريال
                                                                    </td>
                                                                </tr>
                                                                @if($object->getOrder->cobon)
                                                                    <tr>
                                                                        <th>
                                                                            كود الخصم
                                                                        </th>
                                                                        <td>
                                                                            <span style="direction: ltr;float: left;">{{ @$object->getOrder->getOrder->getCobon->code }} -  {{ @$object->getOrder->getCobon->percent }} %</span>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>
                                                                            السعر الاجمالي بعد الخصم
                                                                        </th>
                                                                        <td>
                                                                            {{ $object->getOrder->price_after_discount }} ريال
                                                                        </td>
                                                                    </tr>
                                                                @endif

                                                            </table>
                                                        </div>
                                                    </div>
                                                    <hr>
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
                                @else
                                _________
                                @endif
                            </td>

                        </tr>
                        @php $i++ @endphp
                    @endforeach

                    </tbody>
                </table>

            </div>
            <!-- /basic example -->


            <!-- Restore column visibility -->


            <!-- State saving -->

            <!-- Column groups -->



            <!-- Footer -->
        @include('admin.footer')
        <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
    <!-- /main content -->
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('change','.drag_name',function () {
                var vals = $(this).val();
                var user_id = $(this).attr('user_id');
                $.get('/admin-panel/change_drag_name/'+user_id+'/'+vals,function (data) {

                })
            })
        });
    </script>
    </body>
    </html>

@stop
