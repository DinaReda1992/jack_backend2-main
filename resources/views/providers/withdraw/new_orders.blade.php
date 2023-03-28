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
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم</span> -
                        عرض الطلبات الجديدة</h4>
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
                    <li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li class="active"><a href="">عرض الطلبات الجديدة</a></li>
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
                    <h5 class="panel-title">عرض الطلبات الجديدة</h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>نوع الطلب</th>
                        <th>تاريخ الطلب</th>
                        <th>صاحب الطلب</th>
                        <th>توصيل من </th>
                        <th>توصيل الى </th>
                        <th>تكلفة التوصيل</th>
                        <th>عرض الطلب</th>
                        {{--<th>الاجراء المتخذ</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $i=1;
                    @endphp
                    @foreach($objects as $object)
                        <tr parent_id="{{ $object->id }}">

                            <td>{{ $i }}</td>
                            <td>{{ @$object->getService ?  @$object->getService->name : "_______"  }}</td>
                            <td>{{ $object->created_at->diffForHumans() }}</td>
                            <td>{{ @$object->getUser->username }}</td>
                            <td>{{ @$object->from_name }} ( {{ @$object->from_address }} ) </td>
                            <td>{{ @$object->to_name }} ( {{ @$object->to_address }} ) </td>
                            <td>{{ @$object->price_after_discount }}   ريال </td>
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
                            <td>
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
                                                    <div class="col-sm-8">{{ @$object->getService->name }}</div>
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
                                                        <a target="_blank" href="http://maps.google.com/maps?q={{ $object->from_lat }},{{ $object->from_long }}">{{ @$object->from_name }} ( {{ @$object->from_address }} ) </a></div>
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <label class="col-sm-4"><strong> توصيل الى</strong></label>
                                                    <div class="col-sm-8">
                                                        <a target="_blank" href="http://maps.google.com/maps?q={{ $object->to_lat }},{{ $object->to_long }}">{{ @$object->to_name }} ( {{ @$object->to_address }} ) </a></div>
                                                </div>
                                                <hr>
                                                @if($object->description)
                                                <div class="row">
                                                    <label class="col-sm-4"><strong> وصف الطلب</strong></label>
                                                    <div class="col-sm-8">{{ @$object->description }}</div>
                                                </div>
                                                <hr>
                                                @endif
                                                @if($object->photo)
                                                <div class="row">
                                                    <label class="col-sm-4"><strong> صورة مرفقة</strong></label>
                                                    <div class="col-sm-8"><img width="200" height="200" src="/uploads/{{ $object->photo }}"></div>
                                                </div>
                                                <hr>
                                                @endif
                                                    <div class="row">
                                                        <label class="col-sm-4"><strong> سعر التوصيل</strong></label>
                                                        <div class="col-sm-8">
                                                            <table class="table">
                                                                <tr>
                                                                    <th>
                                                                        سعر التوصيل للكيلو
                                                                    </th>
                                                                    <td>
                                                                        {{ $object->price_for_km }} ريال
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>
                                                                        مسافة التوصيل
                                                                    </th>
                                                                    <td>
                                                                        {{ $object->distance }} KM

                                                                    </td>
                                                                </tr>

                                                                <tr>
                                                                    <th>
                                                                        السعر الاجمالي
                                                                    </th>
                                                                    <td>
                                                                        {{ $object->final_price }} ريال
                                                                    </td>
                                                                </tr>
                                                                @if($object->cobon)
                                                                <tr>
                                                                            <th>
                                                                                كود الخصم
                                                                            </th>
                                                                            <td>
                                                                                <span style="direction: ltr;float: left;">{{ @$object->getCobon->code }} -  {{ @$object->getCobon->percent }} %</span>
                                                                            </td>
                                                                </tr>
                                                                <tr>
                                                                    <th>
                                                                        السعر الاجمالي بعد الخصم
                                                                    </th>
                                                                    <td>
                                                                        {{ $object->price_after_discount }} ريال
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

                            </td>
                            {{--<td align="center" class="center"> <ul class="icons-list">--}}
                            {{--<li class="text-primary-600"><a href="/edit-project/{{ $object->id }}"><i class="icon-pencil7"></i></a></li>--}}
                            {{--<li class="text-danger-600"><a onclick="return false;" object_id="{{ $object->id }}" delete_url="/provider-panel/projects/{{ $object->id }}"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>--}}
                            {{--<li class="text-teal-600"><a href="/refresh-ads/{{ $object->id }}"><i class="glyphicon glyphicon-refresh"></i></a></li>--}}
                            {{--</ul>--}}
                            {{--</td>--}}
                        </tr>

                        @php
                            $i++;
                        @endphp
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
        // Warning alert

    </script>
    </body>
    </html>

@stop
